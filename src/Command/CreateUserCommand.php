<?php

namespace EDB\AdminBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use EDB\AdminBundle\Util\StringUtils;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    public function __construct(
        protected ManagerRegistry $doctrine,
        protected UserPasswordHasherInterface $passwordHasher,
        protected ?string $userClass,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('admin:create-user')
            ->setDescription('Create a new admin user')
            ->addArgument('role', InputArgument::REQUIRED)
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (empty($this->userClass)) {
            throw new Exception('No user class defined for project.');
        }

        $username = $input->getArgument('username');
        $roles = explode(',', $input->getArgument('role'));
        $plainPassword = $input->getArgument('password');

        $roles = array_map(function($role) {
            return trim($role);
        }, $roles);

        $user = new $this->userClass();

        $user->setUsername($username);
        $user->setRoles($roles);

        if (!empty($plainPassword)) {
            $user->setSalt(StringUtils::generateRandomString());
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        }

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush($user);

        $output->writeln('User is successfully created.');

        return Command::SUCCESS;
    }
}
