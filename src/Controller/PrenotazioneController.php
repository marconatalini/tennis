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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/prenotazione")
 */
class PrenotazioneController extends AbstractController
{

    /**
     * @Route("/", name="prenotazione_index")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('tabellone/tabellone.html.twig');
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
        $prenotazione = new Prenotazione();

        $form = $this->createForm(PrenotazioneType::class, $prenotazione);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $prenotazione = $form->getData();
            if (null === $prenotazione->getTitle()){
                $prenotazione->setTitle($this->getUser()->getUsername());
            }
            $ore = $request->get('prenotazione')['ore'];
            $end =  clone $prenotazione->getStart();
            date_modify($end, "+". $ore ." hour");
            $prenotazione->setEnd($end);
            $prenotazione->setTimestamp();

            if ($prenotazioneRepository->findPrenotazioneOggi() !== []){
                $this->addFlash('danger', 'Spiacenti, hai già fatto una prenotazione oggi.');
            }else{
                $em = $this->getDoctrine()->getManager();
                $em->persist($prenotazione);
                $em->flush();
            }
            return $this->redirectToRoute('tabellone_index');
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