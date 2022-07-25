<?php

namespace EDB\AdminBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use EDB\AdminBundle\Util\StringUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    private ManagerRegistry $doctrine;

    private UserPasswordHasherInterface $passwordHasher;

    private string $userClass;

    public function __construct(ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher, string $userClass)
    {
        parent::__construct();

        $this->doctrine = $doctrine;
        $this->passwordHasher = $passwordHasher;
        $this->userClass = $userClass;
    }

    protected function configure(): void
    {
        $this->setName('admin:create-user')
            ->setDescription('Create a new admin user')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('role', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');
        $roles = explode(',', $input->getArgument('role'));

        $roles = array_map(function($role) {
            return trim($role);
        }, $roles);

        $user = new $this->userClass();
        $user->setUsername($username);
        $user->setRoles($roles);

        if ($plainPassword) {
            $user->setSalt(StringUtils::generateRandomString());
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush($user);

        $output->writeln('User is successfully created.');

        return Command::SUCCESS;
    }
}
