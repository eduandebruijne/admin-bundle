<?php

namespace EDB\AdminBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AuthController
{
    private ClientRegistry $clientRegistry;
    private Environment $twig;
    private RouterInterface $router;

    public function __construct(ClientRegistry $clientRegistry, Environment $twig, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->twig = $twig;
        $this->router = $router;
    }

    public function login(): Response
    {
        return new Response($this->twig->render('@EDBAdmin/login.html.twig'));
    }

    public function connect(): RedirectResponse
    {
        return $this->clientRegistry->getClient('google')->redirect(['email', 'profile'], []);
    }

    public function check(): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('dashboard'));
    }
}
