<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 04/05/2019
 * Time: 22:25
 */

namespace App\Controller;

use App\Entity\Contatto;
use App\Form\ContattoType;
use App\Repository\PrenotazioneRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {

        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository)
    {
        $count = $userRepository->countPlayer();

//        return $this->render('default/home.html.twig', [
        return $this->render('default/fullpage.html.twig', [
            'count' => $count
        ]);
    }

    /**
     * @Route("/regolamento", name="regolamento")
     */
    public function regolamento()
    {
        return $this->render('default/regolamento.html.twig');
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        return $this->render('default/faq.html.twig');
    }

    /**
     * @Route("/contatto", name="contatto")
     */
    public function contatto(Request $request)
    {
        /** @var  $contatto Contatto*/
        $contatto = new Contatto();

        $form = $this->createForm(ContattoType::class, $contatto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $contatto = $form->getData();
            $this->contattoMail($contatto->getMessaggio(), $contatto->getEmail());

            $this->addFlash('success', 'Grazie per la tua segnalazione.');

            return $this->redirectToRoute('home');
        }

        return $this->render('default/contattaci.html.twig',[
            'form' => $form->createView()
        ]);
    }

    public function contattoMail($txt, $email)
    {
        $message = (new \Swift_Message('Fantecolo Tennis: contatto'))
            ->setFrom('noreply@fantecolotennis.it')
            ->setTo('staff@fantecolotennis.it')
            ->setBody(
                $this->renderView('default/contatto.html.twig',[
                        'messaggio' => $txt,
                        'email' => $email
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * @Route("/stats", name="statistiche")
     */

    public function statistiche(PrenotazioneRepository $prenotazioneRepository)
    {
        $oreUsers = $prenotazioneRepository->orePrenotateAnnoMeseUser();
        $oreAnyone = $prenotazioneRepository->orePrenotateAnnoMeseAnyone();

        return $this->render('default/stats.html.twig', [
            'oreUser' => $oreUsers,
            'oreAnyone' => $oreAnyone
        ]);
    }
}