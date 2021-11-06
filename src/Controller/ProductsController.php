<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractFOSRestController
{
    private ProductRepository $productRepository;
    private PaginatorInterface $paginator;

    /**
     * @param ProductRepository $productRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(ProductRepository $productRepository, PaginatorInterface $paginator)
    {
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
    }

    #[IsGranted('ROLE_PRODUCTS_CATALOG_ACCESS')]
    public function index(Request $request): Response
    {
        $pagination = $this->paginator->paginate(
            $this->productRepository->createQueryBuilder('p')->orderBy('p.name', 'ASC'),
            $request->query->getInt('page', 1),
            $this->getParameter('api_items_per_page')
        );

        return $this->handleView($this->view($pagination->getItems()));
    }

    #[IsGranted('ROLE_PRODUCTS_CATALOG_ACCESS')]
    public function show(Product $product): Response
    {
        return $this->handleView($this->view($product));
    }
}
