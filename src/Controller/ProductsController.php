<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Controller;

use App\Api\Traits\FormViewTrait;
use App\Entity\Product;
use App\Form\Product\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractFOSRestController
{
    use FormViewTrait;

    private ProductRepository $productRepository;
    private PaginatorInterface $paginator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    )
    {
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
    }

    #[IsGranted('ROLE_PRODUCTS_CATALOG_ACCESS')]
    public function index(Request $request): Response
    {
        $pagination = $this->paginator->paginate(
            $this->productRepository->createQueryBuilder('p')->orderBy('p.createdAt', 'DESC'),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', $this->getParameter('api_items_per_page'))
        );

        return $this->handleView($this->view($pagination->getItems()));
    }

    #[IsGranted('ROLE_PRODUCTS_CATALOG_ACCESS')]
    public function show(Product $product): Response
    {
        return $this->handleView($this->view($product));
    }

    #[IsGranted('ROLE_PRODUCT_CREATE')]
    public function create(Request $request): Response
    {
        return $this->runNormalFormAction($request, ProductType::class, new Product());
    }

    #[IsGranted('ROLE_PRODUCT_UPDATE')]
    public function update(Request $request, Product $product): Response
    {
        return $this->runNormalFormAction(
            $request,
            ProductType::class,
            $product,
            true,
            function (Product $product) {
                return [
                    'price' => $product->getPrice(),
                    'name' => $product->getName(),
                ];
            });
    }

    #[IsGranted('ROLE_PRODUCT_DELETE')]
    public function delete(Product $product): Response
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->handleView($this->view(['success' => true]));
    }
}
