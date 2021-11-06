<?php

namespace App\Cart\Message;

use Symfony\Component\Security\Core\User\UserInterface;

final class CreateCartMessage
{
    private UserInterface $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
