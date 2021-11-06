<?php

namespace App\Tests\Api\Products;

use App\Entity\Product;
use App\Tests\AbstractApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductsTest extends AbstractApiTestCase
{
    public function testGetIndex(): void
    {
        $products = $this->apiRequest('GET', $this->getRouter()->generate('app_api_products_index'));
        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertGreaterThan(1, $products);
        $firstProduct = $products[0];

        $this->assertArrayHasKey('id', $firstProduct);
        $this->assertArrayHasKey('name', $firstProduct);
        $this->assertArrayHasKey('slug', $firstProduct);
        $this->assertArrayHasKey('price', $firstProduct);
        $this->assertArrayHasKey('created_at', $firstProduct);
        $this->assertArrayHasKey('updated_at', $firstProduct);

        $this->assertIsString($firstProduct['id']);
        $this->assertIsString($firstProduct['name']);
        $this->assertIsString($firstProduct['slug']);
        $this->assertIsFloat($firstProduct['price']);
        $this->assertIsString($firstProduct['created_at']);
        $this->assertIsString($firstProduct['updated_at']);
    }

    public function testGetShow(): void
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy([]);
        $productId = $product->getId();

        $result = $this->apiRequest('GET', $this->getRouter()->generate('app_api_products_show', [
            'id' => $productId
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('slug', $result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);

        $this->assertIsString($result['id']);
        $this->assertIsString($result['name']);
        $this->assertIsString($result['slug']);
        $this->assertIsFloat($result['price']);
        $this->assertIsString($result['created_at']);
        $this->assertIsString($result['updated_at']);
    }

    public function testCreate(): void
    {
        $name = uniqid();
        $price = rand(1, 1000);

        $result = $this->apiRequest('POST', $this->getRouter()->generate('app_api_products_create'), [
            'name' => $name,
            'price' => $price
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertTrue($result['isValidRequest']);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);

        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['name' => $name]);

        $this->assertNotNull($product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());
    }

    public function testUpdate(): void
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy([]);
        $productId = $product->getId();

        $name = uniqid();
        $price = rand(1, 1000);

        $result = $this->apiRequest('PUT', $this->getRouter()->generate('app_api_products_update', [
            'id' => $productId
        ]), [
            'name' => $name,
            'price' => $price
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $this->assertTrue($result['isValidRequest']);
        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);

        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

        $this->assertNotNull($product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());
    }

    public function testDelete(): void
    {
        $name = uniqid();
        $price = rand(1, 1000);

        $this->apiRequest('POST', $this->getRouter()->generate('app_api_products_create'), [
            'name' => $name,
            'price' => $price
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());

        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['name' => $name]);
        $this->assertNotNull($product);
        $productId = $product->getId();

        $result = $this->apiRequest('DELETE', $this->getRouter()->generate('app_api_products_delete', [
            'id' => $productId,
        ]));

        $this->assertEquals(Response::HTTP_OK, $this->getBrowser()->getResponse()->getStatusCode());
        $this->assertTrue($result['success']);

        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);
        $this->assertNotNull($product);
        $this->assertNotNull($product->getDeletedAt());
    }
}
