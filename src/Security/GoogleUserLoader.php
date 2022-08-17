<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Security;

use Doctrine\ORM\EntityManagerInterface;

class GoogleUserLoader
{
    private EntityManagerInterface $entityManager;
    private string $userClass;

    public function __construct(EntityManagerInterface $entityManager, string $userClass)
    {
        $this->entityManager = $entityManager;
        $this->userClass = $userClass;
    }

    public function load(string $emailAddress)
    {
        $repo = $this->entityManager->getRepository($this->userClass);

        return $repo->findOneBy(['username' => $emailAddress]);
    }
}
