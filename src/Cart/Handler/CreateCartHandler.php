<?php

namespace App\Cart\Handler;

use App\Cart\Message\CreateCartMessage;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateCartHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateCartMessage $message): string
    {
        $cart = new Cart();
        $cart->setUser($message->getUser());

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        return (string)$cart->getId();
    }
}
