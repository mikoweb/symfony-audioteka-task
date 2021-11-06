<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ProductsFixture extends AbstractFixture implements
    OrderedFixtureInterface,
    FixtureGroupInterface,
    ORMFixtureInterface,
    ContainerAwareInterface
{
    private string $productsPath;

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['products'];
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->productsPath = $container->getParameter('kernel.project_dir') . '/fixtures/products.json';
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $products = json_decode(file_get_contents($this->productsPath));

        foreach ($products as $product) {
            $this->createProduct($manager, $product->name, $product->price);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): int
    {
        return 100;
    }

    private function createProduct(
        ObjectManager $manager,
        string $name,
        float $price
    ): Product
    {
        if (($product = $manager->getRepository(Product::class)->findOneBy(['name' => $name])) === null) {
            $product = new Product();
        }

        $product->setName($name);
        $product->setPrice($price);

        $manager->persist($product);

        return $product;
    }
}
