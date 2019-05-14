<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 05/05/2019
 * Time: 11:02
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tabellone")
 */
class TabelloneController extends AbstractController
{
    /**
     * @Route("/", name="tabellone_index")
     */
    public function index()
    {
        return $this->render("tabellone/tabellone.html.twig");
    }

}