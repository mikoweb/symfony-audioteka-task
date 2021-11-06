<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class ApiKeyProvider implements UserProviderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByApiKey(string $apiKey): ?User
    {
        return $this->userRepository->findApiKeyUser($apiKey);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): ?UserInterface
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return is_subclass_of($class, User::class);
    }
}
