<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItem[]    findAll()
 * @method CartItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function findByProduct(Cart $cart, Product $product): ?CartItem
    {
        try {
            $item = $this->createQueryBuilder('i')
                ->andWhere('i.cart = :cart_id')
                ->setParameter('cart_id', $cart->getId(), 'uuid')
                ->andWhere('i.product = :product_id')
                ->setParameter('product_id', $product->getId(), 'uuid')
                ->getQuery()
                ->getSingleResult()
            ;
        } catch (NoResultException $exception) {
            $item = null;
        }

        return $item;
    }

    public function findById(Cart $cart, string $id): ?CartItem
    {
        try {
            $item = $this->createQueryBuilder('i')
                ->andWhere('i.cart = :cart_id')
                ->setParameter('cart_id', $cart->getId(), 'uuid')
                ->andWhere('i.id = :id')
                ->setParameter('id', $id, 'uuid')
                ->getQuery()
                ->getSingleResult()
            ;
        } catch (NoResultException $exception) {
            $item = null;
        }

        return $item;
    }
}

