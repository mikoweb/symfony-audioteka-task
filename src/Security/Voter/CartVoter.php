<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Security\Voter;

use App\Entity\Admin;
use App\Entity\Cart;
use App\Entity\Page;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

final class CartVoter extends Voter
{
    const CART_ACCESS = 'cart_access';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Cart && in_array($attribute, [
            self::CART_ACCESS,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param Cart $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::CART_ACCESS) {
            return $subject->getUser()->getId() === $user->getId();
        }

        return false;
    }
}
