<?php

namespace App\Entity\Interfaces;

interface GroupInterface
{
    const NAME_ADMIN = 'groups.names.admin';
    const NAME_NORMAL_USER = 'groups.names.normal_users';

    public function __toString(): string;
    public function getName(): string;
    public function setName(string $name): self;

    public function addRole(RoleInterface $role): self;
    public function hasRole(RoleInterface $role): bool;
    public function getRoles(): array;
    public function removeRole(RoleInterface $role): self;
    public function setRoles(array $roles): self;

    public function setGroupRole(string $groupRole): self;
    public function getGroupRole(): string;
}
