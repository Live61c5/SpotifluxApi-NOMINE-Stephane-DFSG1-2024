<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Add a short description for your command',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addOption('password', null, InputOption::VALUE_NONE, 'Mot de passe')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        if ($input->getOption('password')) 
        {
            $password = $input->getOption('password');
        }
        else
        {
            $password = "azerty";
        }

        $admin = new User();
        $admin
            ->setEmail($email)
            ->setPassword($this->passwordHasher->hashPassword($admin, $password))
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"]);

        $this->em->persist($admin);
        $this->em->flush();

        if($admin->getId())
        {
            $io->success('Admin créé.e avec succès !!');
            return Command::SUCCESS;
        }
        else
        {
            $io->error('Une erreur est survenue lors de la création...');
            return Command::FAILURE;
        }

    }
}
