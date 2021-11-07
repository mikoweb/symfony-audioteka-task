<?php

namespace App\Tests\Api\Cart;

use App\Entity\Cart;
use App\Entity\Product;
use App\Tests\AbstractApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CartTest extends AbstractApiTestCase
{
    public function testCreate(): void
    {
        $result = $this->apiRequest('POST', $this->getRouter()->generate('app_api_cart_create'));
        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('cartId', $result);
        $this->assertTrue($result['success']);
        $this->assertIsString($result['cartId']);

        $cartId = $result['cartId'];
        $product = $this->getDoctrine()->getRepository(Cart::class)->find($cartId);
        $this->assertNotNull($product);
    }

    public function testGetIndex(): void
    {
        $this->apiRequest('GET', $this->getRouter()->generate('app_api_cart_index'));
        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());
    }

    public function testCart(): void
    {
        $result = $this->apiRequest('POST', $this->getRouter()->generate('app_api_cart_create'));
        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());
        $cartId = $result['cartId'];

        $products = $this->getDoctrine()->getRepository(Product::class)->findBy([], null, 2);
        $this->assertEquals(2, count($products));
        $currentTotal = $products[0]->getPrice() * 3 + $products[1]->getPrice() * 1;

        $result = $this->apiRequest('POST', $this->getRouter()->generate('app_api_cart_add_items', [
            'id' => $cartId,
        ]), [
            [
                'productId' => $products[0]->getId(),
                'quantity' => 3
            ],
            [
                'productId' => $products[1]->getId(),
                'quantity' => 1
            ]
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);

        $result = $this->apiRequest('GET', $this->getRouter()->generate('app_api_cart_show', [
            'id' => $cartId,
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('total_price', $result['summary']);
        $this->assertEquals($currentTotal, $result['summary']['total_price']);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
        $this->assertNotEmpty($result['items']);
        $this->assertCount(2, $result['items']);

        $map = [
            $products[0]->getId() => null,
            $products[1]->getId() => null,
        ];

        foreach ($result['items'] as $item) {
            $map[$item['product']['id']] = $item['id'];
        }

        $result = $this->apiRequest('DELETE', $this->getRouter()->generate('app_api_cart_remove_items', [
            'id' => $cartId,
        ]), [
            [
                'cartItemId' => $map[$products[0]->getId()],
                'decreaseQuantity' => 2
            ],
            [
                'cartItemId' => $map[$products[1]->getId()],
                'decreaseQuantity' => 1
            ]
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);

        $currentTotal = $products[0]->getPrice() * 1;

        $result = $this->apiRequest('GET', $this->getRouter()->generate('app_api_cart_show', [
            'id' => $cartId,
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('total_price', $result['summary']);
        $this->assertEquals($currentTotal, $result['summary']['total_price']);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
        $this->assertCount(1, $result['items']);

        $result = $this->apiRequest('DELETE', $this->getRouter()->generate('app_api_cart_remove_items', [
            'id' => $cartId,
        ]), [
            [
                'cartItemId' => $map[$products[0]->getId()],
                'decreaseQuantity' => 1
            ]
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);

        $result = $this->apiRequest('GET', $this->getRouter()->generate('app_api_cart_show', [
            'id' => $cartId,
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertArrayHasKey('summary', $result);
        $this->assertArrayHasKey('total_price', $result['summary']);
        $this->assertEquals(0, $result['summary']['total_price']);

        $this->assertArrayHasKey('items', $result);
        $this->assertIsArray($result['items']);
        $this->assertEmpty($result['items']);
    }
}
