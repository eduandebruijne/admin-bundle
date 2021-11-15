<?php

namespace EDB\AdminBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AuthController
{
    private ClientRegistry $clientRegistry;
    private Environment $twig;
    private RouterInterface $router;

    /**
     * @param ClientRegistry $clientRegistry
     * @param Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(ClientRegistry $clientRegistry, Environment $twig, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * @Route("/login", priority=500, name="login")
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(): Response
    {
        return new Response($this->twig->render('@EDBAdmin/login.html.twig'));
    }

    /**
     * @Route("/login/google/connect", name="connect_google")
     */
    public function connect(): RedirectResponse
    {
        return $this->clientRegistry->getClient('google')->redirect(['email', 'profile'], []);
    }

    /**
     * @Route("/login/google/connect/check", name="connect_google_check")
     */
    public function check(): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('dashboard'));
    }
}
