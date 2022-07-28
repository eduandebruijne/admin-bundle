<?php

namespace EDB\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AuthController
{
    private Environment $twig;
    private RouterInterface $router;

    public function __construct(Environment $twig, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->router = $router;
    }

    public function login(): Response
    {
        return new Response($this->twig->render('@EDBAdmin/login.html.twig'));
    }

    public function check(): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('dashboard'));
    }
}
