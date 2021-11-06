<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Cart;

use App\Entity\Cart;
use App\Entity\CartItem;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

#[ExclusionPolicy('all')]
final class CartSummary
{
    private Cart $cart;
    #[Expose]
    private float $totalPrice;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
        $this->generate();
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    private function generate(): void
    {
        $items = $this->cart->getItems()->toArray();
        $this->totalPrice = array_reduce($items, function (?float $carry, CartItem $item) {
            return $carry + $item->sumPrice();
        });
    }
}
