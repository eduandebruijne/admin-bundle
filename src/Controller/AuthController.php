<?php

namespace EDB\AdminBundle\Controller;

use EDB\AdminBundle\Security\GoogleHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AuthController
{
    public function __construct(
        protected Environment $twig,
        protected RouterInterface $router,
        protected GoogleHelper $googleHelper
    ) {
    }

    public function login(): Response
    {
        return new Response($this->twig->render('@EDBAdmin/login.html.twig'));
    }

    public function check(): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('dashboard'));
    }

    public function startGoogleLogin()
    {
        return new RedirectResponse($this->googleHelper->getLoginUrl());
    }

    public function logout()
    {
        return new RedirectResponse('/');
    }
}
