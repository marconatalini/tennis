<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 04/05/2019
 * Time: 22:25
 */

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository)
    {
        $count = $userRepository->countPlayer();

        return $this->render('default/home.html.twig', [
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


}