<?php

namespace App\Entity\Interfaces;

interface RoleInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __toString(): string;
    public function getRole(): string;
    public function getName(): string;
}
