<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Security;

use Doctrine\ORM\EntityManagerInterface;

class GoogleUserLoader
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ?string $userClass
    ) {
    }

    public function load(string $emailAddress)
    {
        if (empty($this->userClass)) {
            throw new Exception('No user class defined for project.');
        }

        $repo = $this->entityManager->getRepository($this->userClass);

        return $repo->findOneBy(['username' => $emailAddress]);
    }
}
