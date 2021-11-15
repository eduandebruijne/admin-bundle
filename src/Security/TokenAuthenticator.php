<?php

namespace EDB\AdminBundle\Security;

use Doctrine\Persistence\ManagerRegistry;
use EDB\AdminBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    const HEADER_NAME = 'X-APP-TOKEN';
    const REDIRECT_SESSION_KEY = 'edb-admin-bundle-attempt-uri';

    private ManagerRegistry $doctrine;
    private TokenManager $tokenManager;

    public function __construct(ManagerRegistry $doctrine, TokenManager $tokenManager)
    {
        $this->doctrine = $doctrine;
        $this->tokenManager = $tokenManager;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_NAME);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $token = $request->headers->get(self::HEADER_NAME);
        $this->tokenManager->validate($token);

        $user = $this->doctrine->getRepository(User::class)->findOneBy([
            'token' => $token
        ]);

        if (empty($user)) {
            throw new AccessDeniedException();
        }

        return new SelfValidatingPassport(new UserBadge($token, function ($userIdentifier) use ($user) {
            return $user;
        }));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }
}