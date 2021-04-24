<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 05/05/2019
 * Time: 21:48
 */

namespace App\Controller;


use App\Entity\Prenotazione;
use App\Entity\User;
use App\Form\PrenotazioneType;
use App\Repository\PrenotazioneRepository;
use App\Repository\UserRepository;
use DateTimeZone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/prenotazione")
 */
class PrenotazioneController extends AbstractController
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(\Swift_Mailer $mailer, SessionInterface $session)
    {

        $this->mailer = $mailer;
        $this->session = $session;
    }

    /**
     * @Route("/", name="prenotazione_index")
     */
    public function index(PrenotazioneRepository $prenotazioneRepository)
    {
        if (new \DateTime() >= new \DateTime('2021-03-04')) {
            $this->addFlash('danger', "Non è attualmente consentito l'uso del campo. Ordinanza 714 del 04.03.21 Regione Lombardia");
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        $idsPrenotazioniGiocatore = $prenotazioneRepository->findIdsPrenotati($user);
//        $prenotazioniOggi = $prenotazioneRepository->findPrenotazioneOggi($user);
        $prenotazioniOggi = $prenotazioneRepository->findPrenotazioniLast24ore($user);

//        var_dump($prenotazioniOggi);die;

        return $this->render('tabellone/tabellone.html.twig', [
            'idsPrenotazioniGiocatore' => $idsPrenotazioniGiocatore,
            'prenotazioniOggi' => $prenotazioniOggi,
        ]);
    }

    /**
     * @Route("/elimina", name="prenotazione_elimina")
     * @IsGranted("ROLE_USER")
     */
    public function elimina(Request $request, PrenotazioneRepository $prenotazioneRepository)
    {
        $prenotazione = $prenotazioneRepository->find($request->get('id'));
        if (null !== $prenotazione) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($prenotazione);
            $em->flush();
        }

        $this->addFlash('success', 'Hai cancellato la tua prenotazione.');

        return $this->redirectToRoute('prenotazione_index');
    }

    /**
     * @Route("/liberacampo", name="prenotazione_libera_campo")
     */
    public function liberaCampo(PrenotazioneRepository $prenotazioneRepository)
    {
        /* @var Prenotazione $prenotazione */
        $prenotazione = $prenotazioneRepository->findCurrent();

        if ($prenotazione !== null && $this->session->get('prenotato') == 'prenotato'){
            $prenotazione->setEnd(new \DateTime('now', new DateTimeZone('Europe/Rome')));
            $em = $this->getDoctrine()->getManager();
            $em->persist($prenotazione);
            $em->flush();
            $this->addFlash('info', 'Campo libero. Grazie.');
            $this->session->remove('prenotato');
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/giocoadesso", name="prenotazione_prenota_adesso")
     */
    public function prenotaAdessoUnOra(UserRepository $repository, PrenotazioneRepository $prenotazioneRepository)
    {
        if ($this->session->get('prenotato') !== null){
            $this->addFlash('info', "Stai già occupando il campo. Liberalo se hai finito.");
            return $this->redirectToRoute('home');
        }

        /** @var  $prenotazione Prenotazione*/
        $prenotazione = new Prenotazione();

        if ($this->getUser()) {
            /** @var User $user */
            $user = $this->getUser();
            $prenotazione->setTitle('ora gioca: '.$user->getUsername());
        } else {
            $user = $repository->find(1);
            $niks = [
                'novak djokovic','rafael nadal','daniil medvedev','dominic thiem','roger federer','stefanos tsitsipas','alexander zverev','andrey rublev','diego schwartzman','matteo berrettini','denis shapovalov','gael monfils','roberto bautista agut','david goffin','milos raonic','pablo carreno busta','grigor dimitrov','fabio fognini','felix auger aliassime','stan wawrinka','karen khachanov','cristian garin','alex de minaur','casper ruud','john isner','borna coric','dusan lajovic','daniel evans','benoit paire','hubert hurkacz','ugo humbert','filip krajinovic','taylor fritz','jannik sinner','lorenzo sonego','adrian mannarino','jan lennard struff','reilly opelka','john millman','nikoloz basilashvili','marin cilic','miomir kecmanovic','alexander bublik','aslan karatsev','kei nishikori','albert ramos vinolas','guido pella','nick kyrgios','richard gasquet','jordan thompson','alejandro davidovich fokina','kyle edmund','tennys sandgren','yoshihito nishioka','sam querrey','tommy paul','pablo andujar','feliciano lopez','marton fucsovics','laslo djere','aljaz bedene','frances tiafoe','stefano travaglia','jeremy chardy','cameron norrie','jo wilfried tsonga','vasek pospisil','jiri vesely','gilles simon','fernando verdasco','dominik koepfer','corentin moutet','pablo cuevas','thiago monteiro','radu albot','egor gerasimov','ricardas berankis','soonwoo kwon','salvatore caruso','marcos giron','lucas pouille','alexei popyrin','steve johnson','lloyd harris','federico coria','federico delbonis','emil ruusuvuori','kevin anderson','marco cecchinato','juan ignacio londero','pedro martinez','sebastian korda','pierre hugues herbert','mikael ymer','norbert gombos','mikhail kukushkin','roberto carballes baena','dennis novak','attila balazs','joao sousa'
            ];
            $prenotazione->setTitle($niks[array_rand($niks)]);
        }

        $start = new \DateTime('now', new DateTimeZone('Europe/Rome'));
        $end = clone $start;
        date_modify($end, '+1 hour');
        $prenotazione->setUser($user)
//            ->setStart(new \DateTime('now +2 hour'))
//            ->setEnd(new \DateTime('now +3 hour'))
            ->setStart($start)
            ->setEnd($end)
            ->setTimestamp()
            ;

        if ($prenotazioneRepository->findSovrapposizione($prenotazione->getStart(), $prenotazione->getEnd()) !== []) {
            $this->addFlash('danger', 'Spiacenti, il campo è già occupato.');
            return $this->redirectToRoute('prenotazione_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($prenotazione);
        $em->flush();

        $this->addFlash('info', "Hai occupato il campo per 1 ora. Buon divertimento.");
        //set prenotato in current session
        $this->session->set('prenotato', 'prenotato');

        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/prenota", name="prenotazione_prenota")
     * @IsGranted("ROLE_USER")
     */
    public function prenota(Request $request, PrenotazioneRepository $prenotazioneRepository)
    {
        /**@var $prenotazione Prenotazione*/
        $prenotazione = new Prenotazione();

        $form = $this->createForm(PrenotazioneType::class, $prenotazione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $title = $this->censura($prenotazione->getTitle());
            $prenotazione = $form->getData();
            $prenotazione->setUser($this->getUser());
//            $prenotazione->setTitle($this->getUser()->getUsername(). 'Vs '. $prenotazione->getTitle());
            $prenotazione->setTitle($this->getUser()->getUsername() . $title);
            $ore = $request->get('prenotazione')['ore'];
            $end =  clone $prenotazione->getStart();
            date_modify($end, "+". $ore ." hour");
            $prenotazione->setEnd($end);
            $prenotazione->setTimestamp();
            $mail = $request->get('prenotazione')['email'];

            if ($prenotazioneRepository->findPrenotazioniLast24ore($this->getUser()) !== []){
                $this->addFlash('danger', 'Spiacenti, hai già fatto una prenotazione nelle ultime 36 ore.');
            } elseif ($prenotazioneRepository->findOverlap($prenotazione->getStart(), $end) !== []) {
                $this->addFlash('danger', 'Spiacenti, il campo è già occupato.');
            }else{
                $em = $this->getDoctrine()->getManager();
                $em->persist($prenotazione);
                $em->flush();
                if ($mail) {
                    $this->prenotazioneMail($prenotazione, $mail);
                    $this->addFlash('success', $mail . ' è stato avvisato con una mail.');
                }
            }
            return $this->redirectToRoute('prenotazione_index');
        } else {
            $start = new \DateTime($request->get('ora'));
            $prenotazione->setStart($start);
            $prenotazione->setUser($this->getUser());
            $form->setData($prenotazione);
        }

        return $this->render('prenotazione/prenota.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/json", methods={"POST"}, name="prenotazione_json")
     */
    public function prenotazioneJson(Request $request, PrenotazioneRepository $prenotazioneRepository)
    {
        $user = $this->getUser();
        $start = $request->get('start');
        $end = $request->get('end');
        $result = $prenotazioneRepository->findPrenotazioneWeek($start, $end, $user);
        return $this->json($result);
    }

    /**
     * @Route("/jsonUser", methods={"POST"}, name="prenotazione_jsonUser")
     */
    public function prenotazioneJsonUser(Request $request, PrenotazioneRepository $prenotazioneRepository)
    {
        $user = $this->getUser();
        $start = $request->get('start');
        $end = $request->get('end');
        $result = $prenotazioneRepository->findPrenotazioneWeekUser($start, $end, $user);
        return $this->json($result);
    }

    /**
     * @param Prenotazione $prenotazione
     * @param String $email
     */
    public function prenotazioneMail(Prenotazione $prenotazione, $email)
    {
        $message = (new \Swift_Message('Prenotazione Tennis a Fantecolo'))
            ->setFrom('noreply@fantecolotennis.it')
            ->setTo($email)
            ->setBody(
                $this->renderView('prenotazione/invito.html.twig',[
                        'nome' => $prenotazione->getUser()->getUsername(),
                        'data' => $prenotazione->getStart(),
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    private function censura($testo = ""){
        $badword = file("badword.txt", FILE_IGNORE_NEW_LINES);
        $new = "";

//        dd($badword);

        $word_array = explode(" ", $testo);
        foreach ($word_array as $item) {
            if (array_search($item, $badword)) {
                $new = $new . " !!@@##";
            } else {
                $new = $new . " " . $item;
            }
        }

        if ($new !== "") {
            return " Vs " . $new;
        }

        return $new;
    }
}