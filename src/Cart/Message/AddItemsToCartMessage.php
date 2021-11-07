<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Cart\Message;

use App\Entity\Cart;

final class AddItemsToCartMessage
{
    private Cart $cart;
    private array $items;

    public function __construct(Cart $cart, array $items)
    {
        $this->cart = $cart;
        $this->items = $items;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
