<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Cart;

use App\Cart\Handler\AddItemsToCartHandler;
use App\Cart\Handler\CreateCartHandler;
use App\Cart\Handler\RemoveItemsFromCartHandler;
use App\Cart\Message\AddItemsToCartMessage;
use App\Cart\Message\CreateCartMessage;
use App\Cart\Message\RemoveItemsFromCartMessage;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final class CartMessageBus extends MessageBus
{
    public function __construct(
        CreateCartHandler $createCartHandler,
        AddItemsToCartHandler $addItemsToCartHandler,
        RemoveItemsFromCartHandler $removeItemsFromCartHandler
    )
    {
        parent::__construct([
            new HandleMessageMiddleware(new HandlersLocator([
                CreateCartMessage::class => [$createCartHandler],
                AddItemsToCartMessage::class => [$addItemsToCartHandler],
                RemoveItemsFromCartMessage::class => [$removeItemsFromCartHandler],
            ])),
        ]);
    }
}
