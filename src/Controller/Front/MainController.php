<?php

namespace App\Controller\Front;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(ProductRepository $productRepository): Response
    {
        $products  = $productRepository->findThreeArticleByRand();

        return $this->render('front/main/index.html.twig', [
            'products' => $products
        ]);
    }
}
