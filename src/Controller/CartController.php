<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Controller;

use App\Cart\CartMessageBus;
use App\Cart\CartSummary;
use App\Cart\Message\AddItemsToCartMessage;
use App\Cart\Message\CreateCartMessage;
use App\Cart\Message\RemoveItemsFromCartMessage;
use App\Controller\RequestValidator\CartAddItemsValidator;
use App\Controller\RequestValidator\CartRemoveItemsValidator;
use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Security\Voter\CartVoter;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Throwable;

class CartController extends AbstractFOSRestController
{
    private CartRepository $cartRepository;
    private CartMessageBus $cartMessageBus;

    public function __construct(CartRepository $cartRepository, CartMessageBus $cartMessageBus)
    {
        $this->cartRepository = $cartRepository;
        $this->cartMessageBus = $cartMessageBus;
    }

    #[IsGranted('ROLE_CART_ACCESS')]
    public function index(): Response
    {
        return $this->handleView($this->view($this->cartRepository->getUserCarts($this->getUser())));
    }

    #[IsGranted('ROLE_CART_CREATE')]
    public function create(): Response
    {
        try {
            $result = $this->cartMessageBus->dispatch(new CreateCartMessage($this->getUser()));
        } catch (Throwable $exception) {
            return $this->handleView($this->view(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR));
        }

        return $this->handleView($this->view([
            'success' => true,
            'cartId' => $result->last(HandledStamp::class)->getResult(),
        ]));
    }

    #[IsGranted('ROLE_CART_ACCESS')]
    public function show(Cart $cart): Response
    {
        $this->checkCartAccess($cart);

        return $this->handleView($this->view([
            'cartId' => $cart->getId(),
            'items' => $cart->getItems(),
            'summary' => new CartSummary($cart),
        ]));
    }

    #[IsGranted('ROLE_CART_ADD_ITEMS')]
    public function addItems(Cart $cart, Request $request): Response
    {
        $this->checkCartAccess($cart);
        $items = json_decode($request->getContent(), true) ?? [];

        if (!CartAddItemsValidator::isValid($items)) {
            return $this->handleView($this->view(['success' => false], Response::HTTP_BAD_REQUEST));
        }

        try {
            $this->cartMessageBus->dispatch(new AddItemsToCartMessage($cart, $items));
        } catch (Throwable $exception) {
            return $this->handleView($this->view(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR));
        }

        return $this->handleView($this->view(['success' => true]));
    }

    #[IsGranted('ROLE_CART_REMOVE_ITEMS')]
    public function removeItems(Cart $cart, Request $request): Response
    {
        $this->checkCartAccess($cart);
        $items = json_decode($request->getContent(), true) ?? [];

        if (!CartRemoveItemsValidator::isValid($items)) {
            return $this->handleView($this->view(['success' => false], Response::HTTP_BAD_REQUEST));
        }

        try {
            $this->cartMessageBus->dispatch(new RemoveItemsFromCartMessage($cart, $items));
        } catch (Throwable $exception) {
            return $this->handleView($this->view(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR));
        }

        return $this->handleView($this->view(['success' => true]));
    }

    private function checkCartAccess(Cart $cart): void
    {
        $this->denyAccessUnlessGranted(CartVoter::CART_ACCESS, $cart, 'This cart is not your own.');
    }
}
