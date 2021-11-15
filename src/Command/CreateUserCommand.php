<?php

namespace EDB\AdminBundle\Command;

use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use EDB\AdminBundle\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        parent::__construct();
        $this->doctrine = $doctrine;
    }

    protected function configure()
    {
        $this->setName('admin:create-user')
            ->setDescription('Create a new admin user')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('roles', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $roles = explode(',', $input->getArgument('roles'));

        $roles = array_map(function($role) {
            return trim($role);
        }, $roles);

        $user = new User();
        $user->setUsername($username);
        $user->setRoles($roles);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush($user);

        return 0;
    }
}