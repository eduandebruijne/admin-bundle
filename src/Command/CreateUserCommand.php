<?php

namespace EDB\AdminBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    private ManagerRegistry $doctrine;
    private string $userClass;

    public function __construct(ManagerRegistry $doctrine, string $userClass)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
        $this->userClass = $userClass;
    }

    protected function configure()
    {
        $this->setName('admin:create-user')
            ->setDescription('Create a new admin user')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('role', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('email');
        $roles = explode(',', $input->getArgument('role'));

        $roles = array_map(function($role) {
            return trim($role);
        }, $roles);

        $user = new $this->userClass();
        $user->setUsername($username);
        $user->setRoles($roles);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush($user);

        return 0;
    }
}
