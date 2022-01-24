<?php

namespace App\Controller\Front;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list')]
    public function productList(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('front/product/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function productShow($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        return $this->render('front/product/product.html.twig', [
            'product' => $product,
        ]);
    }
    #[Route('/search', name: 'search')]
    public function search(ProductRepository $productRepository, Request $request)
    {
        $search = $request->query->get('search');

        $products = $productRepository->searchByTerm($search);

        return $this->render('front/product/products.html.twig', [
            'products' => $products,
        ]);
    }
}
