<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route({
     *      "en": "/products",
     *      "fr": "/produits"
     *  },
     *  name="products_list",
     *  methods={"GET"}
     * )
     */
    public function list(): Response
    {
        /** @var ProductRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route({
     *      "en": "/products/{slug}",
     *      "fr": "/produits/{slug}"
     *  },
     *  name="products_view",
     *  methods={"GET"}
     * )
     */
    public function view(Product $product): Response
    {
        return $this->render('product/view.html.twig', [
            'product' => $product,
        ]);
    }
}