<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\DataFixtures;

use App\DataFixtures\Abstracts\AbstractGroupFixture;
use App\Entity\Interfaces\GroupInterface;
use App\Entity\Interfaces\RoleInterface;
use App\Entity\UserGroup;
use App\Entity\UserRole;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserGroupsFixture extends AbstractGroupFixture implements
    OrderedFixtureInterface,
    FixtureGroupInterface,
    ORMFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['users'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var UserGroup $normalApiUser */
        $normalApiUser = $this->createGroup(
            $manager,
            GroupInterface::NAME_NORMAL_API_USER,
            RoleInterface::ROLE_USER
        );

        $this->setReference('Group.NormalApiUser', $normalApiUser);

        foreach ($manager->getRepository(UserRole::class)->findAll() as $role) {
            $this->setReference('Role.' . $role->getRole(), $role);
        }

        $this->loadGroupRolesFromXml('user_group_roles.xml', $manager);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupClass(): string
    {
        return UserGroup::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleClass(): string
    {
        return UserRole::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): int
    {
        return 40;
    }
}
