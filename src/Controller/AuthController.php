<?php

namespace EDB\AdminBundle\Controller;

use EDB\AdminBundle\Security\GoogleHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AuthController
{
    private Environment $twig;
    private RouterInterface $router;
    private GoogleHelper $googleHelper;

    public function __construct(Environment $twig, RouterInterface $router, GoogleHelper $googleHelper)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->googleHelper = $googleHelper;
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
