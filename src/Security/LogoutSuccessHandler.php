<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 05/03/2021
 * Time: 21:58
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }


    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        if ($request->get('action') == 'removeuser'){
            /* @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flash */
            $flash = $request->getSession()->getFlashBag();
            $flash->add('danger', "Abbiamo cancellato il tuo account. Speriamo di rivederti presto.");
        }

        return new RedirectResponse($this->router->generate('home'));

    }
}