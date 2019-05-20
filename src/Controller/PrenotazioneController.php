<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 05/05/2019
 * Time: 21:48
 */

namespace App\Controller;


use App\Entity\Prenotazione;
use App\Form\PrenotazioneType;
use App\Repository\PrenotazioneRepository;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/prenotazione")
 */
class PrenotazioneController extends AbstractController
{

    /**
     * @Route("/", name="prenotazione_index")
     */
    public function index(PrenotazioneRepository $prenotazioneRepository)
    {
        $user = $this->getUser();
        $idsPrenotazioniGiocatore = $prenotazioneRepository->findIdsPrenotati($user);
        $prenotazioniOggi = $prenotazioneRepository->findPrenotazioneOggi($user);

        //var_dump($prenotazioniOggi);die;

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

            $prenotazione = $form->getData();
            $prenotazione->setTitle($this->getUser()->getUsername(). ': '. $prenotazione->getTitle());
            $ore = $request->get('prenotazione')['ore'];
            $end =  clone $prenotazione->getStart();
            date_modify($end, "+". $ore ." hour");
            $prenotazione->setEnd($end);
            $prenotazione->setTimestamp();

            if ($prenotazioneRepository->findPrenotazioneOggi($this->getUser()) !== []){
                $this->addFlash('danger', 'Spiacenti, hai già fatto una prenotazione oggi.');
            } elseif ($prenotazioneRepository->findOverlap($prenotazione->getStart(), $end) !== []) {
                $this->addFlash('danger', 'Spiacenti, il campo è già occupato.');
            }else{
                $em = $this->getDoctrine()->getManager();
                $em->persist($prenotazione);
                $em->flush();
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
     * @Route("/json", methods={"GET"}, name="prenotazione_json")
     */
    public function prenotazioneJson(Request $request, PrenotazioneRepository $prenotazioneRepository)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $result = $prenotazioneRepository->findPrenotazioneWeek($start, $end);
        return $this->json($result);
    }


}