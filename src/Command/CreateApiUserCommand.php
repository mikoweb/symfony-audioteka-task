<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Command;

use App\Entity\Interfaces\GroupInterface;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-api-user',
    description: 'Create new API User',
)]
class CreateApiUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private GroupRepository $groupRepository;
    private UserRepository $userRepository;

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('apikey', InputArgument::REQUIRED, 'Api key')
        ;
    }

    public function __construct(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $apiKey = $input->getArgument('apikey');

        if (is_null($this->userRepository->findUser($username))) {
            $user = new User();
            $user->setUsername($username);
            $user->setApiKey($apiKey);
            $user->addGroup($this->groupRepository->getGroup(GroupInterface::NAME_NORMAL_API_USER));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText(''));

            $token = $config->builder()
                ->withClaim('api_key', $user->getApiKey())
                ->getToken($config->signer(), $config->signingKey())
            ;

            $io->block('TOKEN:');
            $io->block($token->toString());
            $io->success("Api token generated");
        } else {
            $io->warning("User $username is exists. Use app:show-user-api-token command to show token.");
        }

        return Command::SUCCESS;
    }
}
