<?php

namespace App\Cart\Handler;

use App\Cart\Message\RemoveItemsFromCartMessage;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveItemsFromCartHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private CartItemRepository $cartItemRepository;

    public function __construct(EntityManagerInterface $entityManager, CartItemRepository $cartItemRepository)
    {
        $this->entityManager = $entityManager;
        $this->cartItemRepository = $cartItemRepository;
    }

    public function __invoke(RemoveItemsFromCartMessage $message): void
    {
        $cart = $message->getCart();

        foreach ($message->getItems() as $item) {
            $cartItem = $this->cartItemRepository->findById($cart, $item['cartItemId']);

            if (!is_null($cartItem)) {
                $cartItem->increaseQuantity(-$item['decreaseQuantity']);

                if ($cartItem->getQuantity() > 0) {
                    $this->entityManager->persist($cartItem);
                } else {
                    $this->entityManager->remove($cartItem);
                }
            }
        }

        $this->entityManager->flush();
    }
}
