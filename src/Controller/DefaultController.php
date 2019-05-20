<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 04/05/2019
 * Time: 22:25
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/regolamento", name="regolamento")
     */
    public function regolamento()
    {
        return $this->render('default/regolamento.html.twig');
    }

}