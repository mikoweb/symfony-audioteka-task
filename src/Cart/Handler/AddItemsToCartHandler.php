<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Cart\Handler;

use App\Cart\Message\AddItemsToCartMessage;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddItemsToCartHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private CartItemRepository $cartItemRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        CartItemRepository $cartItemRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->cartItemRepository = $cartItemRepository;
    }

    public function __invoke(AddItemsToCartMessage $message): void
    {
        $cart = $message->getCart();

        foreach ($message->getItems() as $item) {
            $product = $this->productRepository->find($item['productId']);
            $cartItem = $this->cartItemRepository->findByProduct($cart, $product);

            if (is_null($cartItem)) {
                $cartItem = new CartItem();
                $cartItem->setQuantity($item['quantity']);
            } else {
                $cartItem->increaseQuantity($item['quantity']);
            }

            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setPrice($product->getPrice());

            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();
    }
}
