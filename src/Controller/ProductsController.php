<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractFOSRestController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[IsGranted('ROLE_PRODUCTS_CATALOG_ACCESS')]
    public function index(): Response
    {
        return $this->handleView($this->view($this->productRepository->findAll()));
    }

    #[IsGranted('ROLE_PRODUCTS_CATALOG_ACCESS')]
    public function show(Product $product): Response
    {
        return $this->handleView($this->view($product));
    }
}
