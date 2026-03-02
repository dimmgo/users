<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-root-user',
    description: 'Create a new root user.',
)]
class CreateRootUserCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('login', InputArgument::REQUIRED, 'The login for the new root user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password for the new root user')
            ->addArgument('phone', InputArgument::REQUIRED, 'The phone number for the new root user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        $phone = $input->getArgument('phone');

        $userRepository = $this->entityManager->getRepository(User::class);
        if ($userRepository->findOneBy(['login' => $login])) {
            $io->error(sprintf('User with the login "%s" already exists.', $login));
            return Command::FAILURE;
        }

        $user = new User();
        $user->setLogin($login);

        $user->setPassword($password);
        $user->setPhone($phone);
        $user->setRoles([User::ROLE_ROOT]);
        $apiToken = $user->createApiToken();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('Bearer Token: %s', $apiToken));
        $io->success(sprintf('Root user "%s" was successfully created!', $login));

        return Command::SUCCESS;
    }
}
