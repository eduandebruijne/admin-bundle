<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class GoogleAuthenticator extends AbstractAuthenticator
{
    private GoogleUserLoader $loader;
    private RouterInterface $router;
    private SessionInterface $session;
    private GoogleHelper $googleHelper;

    public function __construct(GoogleUserLoader $loader, RouterInterface $router, SessionInterface $session, GoogleHelper $googleHelper)
    {
        $this->loader = $loader;
        $this->router = $router;
        $this->session = $session;
        $this->googleHelper = $googleHelper;
    }

    public function supports(Request $request): ?bool
    {
        return $request->query->has('code');
    }

    public function authenticate(Request $request)
    {
        $identifier = $this->googleHelper->getUserIdentifier($request->query->get('code'));
        if (null === $identifier) {
            throw new AuthenticationException();
        }

        return new SelfValidatingPassport(new UserBadge($identifier, [$this->loader, 'load']));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->session->get('_security.main.target_path');

        return new RedirectResponse($targetPath ?? $this->router->generate('dashboard'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->router->generate('login'));
    }
}
