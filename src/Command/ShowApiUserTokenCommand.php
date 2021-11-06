<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Command;

use App\Repository\UserRepository;
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
    name: 'app:show-api-user-token',
    description: 'Show API User token',
)]
class ShowApiUserTokenCommand extends Command
{
    private UserRepository $userRepository;

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
        ;
    }

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        if (($user = $this->userRepository->findUser($username)) !== null) {
            $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($user->getApiKey()));

            $token = $config->builder()
                ->withClaim('api_key', $user->getApiKey())
                ->getToken($config->signer(), $config->signingKey())
            ;

            $io->success("Api token: {$token->toString()}");
        } else {
            $io->error("User $username not found");
        }

        return Command::SUCCESS;
    }
}
