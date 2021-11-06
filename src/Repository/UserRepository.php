<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUser(string $userName): ?User
    {
        return $this->findOneBy([
            'username' => $userName,
        ]);
    }

    public function findApiKeyUser(string $apiKey): ?User
    {
        return $this->findOneBy([
            'apiKey' => $apiKey,
        ]);
    }

    public function getTestUser(): User
    {
        $users = $this->createQueryBuilder('u')
            ->where('u.apiKey IS NOT NULL')
            ->orderBy('u.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        if (count($users) === 0) {
            throw new NoResultException();
        }

        return $users[0];
    }
}
