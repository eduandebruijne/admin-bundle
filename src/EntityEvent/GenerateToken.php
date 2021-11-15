<?php

namespace EDB\AdminBundle\EntityEvent;

use EDB\AdminBundle\Entity\BaseEntity;
use EDB\AdminBundle\Entity\User;
use EDB\AdminBundle\Security\TokenManager;

class GenerateToken extends AbstractEntityEventHandler
{
    private TokenManager $tokenManager;

    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function supports(): array
    {
        return [User::class];
    }

    public function handle(BaseEntity $entity, string $context)
    {
        /** @var User $entity */
        $events = [Pool::CREATE_CONTEXT, Pool::UPDATE_CONTEXT];
        if (empty($entity->getToken()) && in_array($context, $events)) {
            $entity->setToken($this->tokenManager->generate());
        }
    }
}