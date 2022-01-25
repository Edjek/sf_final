<?php

namespace App\Controller\Front;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function productShow(
        $id,
        ProductRepository $productRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {
        $product = $productRepository->find($id);

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $user = $this->getUser();
            if ($user) {
                $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

                $comment->setUser($user);
                $comment->setProduct($product);
                $comment->setDate(new \DateTime("NOW"));

                $entityManagerInterface->persist($comment);
                $entityManagerInterface->flush();

            }
        }

        return $this->render('front/product/product.html.twig', [
            'product' => $product,
            'commentForm' => $commentForm->createView(),
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
