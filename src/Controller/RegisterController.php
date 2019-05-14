<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 07/05/2019
 * Time: 23:01
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
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
     * @Route("/register", name="giocatore_registrazione")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $giocatore = new User();

        $form = $this->createForm(UserType::class, $giocatore);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $password = $passwordEncoder->encodePassword($giocatore, $giocatore->getPlainPassword());
            $giocatore->setPassword($password);
            $giocatore->setActivationKey(substr(md5(rand()), 0, 19));

            $em = $this->getDoctrine()->getManager();
            $em->persist($giocatore);
            $em->flush();

            $this->activationMail($giocatore);

            $this->addFlash('success', 'Ti abbiamo spedito una mail per attivare il tuo account.');

            return $this->redirectToRoute('home');
        }

        return $this->render('user/register.html.twig',[
           'form' => $form->createView()
        ]);
    }

    public function activationMail(User $user)
    {
        $message = (new \Swift_Message('Fantecolo Tennis'))
            ->setFrom('noreply@natalinitrasporti.it')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView('user/activation.html.twig',[
                    'name' => $user->getUsername(),
                    'activationLink' => $user->getActivationKey(),
                    ]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * @Route("/activation", name="giocatore_attivazione")
     */
    public function activation(Request $request, UserRepository $userRepository)
    {
        if (null !== $request->get('activation')){
            $user = $userRepository->findOneBy([
                'activationKey' => $request->get('activation'),
                'username' => $request->get('username'),
            ]);

            if (null !== $user) {
                $user->setActive(true);
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $this->addFlash('success',
                    "Complimenti ". $user->getUsername(). ", ora puoi entrare.");

                return $this->redirectToRoute('app_login', [
                    'error' => null,
                ]);
            }
        }

        $this->addFlash('error', "Ooops! ". $user->getUsername(). ", qualcosa non funziona. Riprova.");

        return $this->render('default/home.html.twig');
    }



}