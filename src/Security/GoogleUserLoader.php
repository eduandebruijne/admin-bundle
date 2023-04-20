<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

class GoogleUserLoader
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ?string $userClass
    ) {
    }

    public function load(string $emailAddress)
    {
        if (null === $this->userClass) {
            throw new Exception('No user class defined for project.');
        }

        $repo = $this->entityManager->getRepository($this->userClass);

        return $repo->findOneBy(['username' => $emailAddress]);
    }
}
