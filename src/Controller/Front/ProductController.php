<?php

namespace App\Controller\Front;

use App\Entity\Like;
use App\Entity\Comment;
use App\Entity\Dislike;
use App\Form\CommentType;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use App\Repository\DislikeRepository;
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

    #[Route('/like/product/{id}', name: 'product_like')]
    public function likeProduct(
        $id,
        ProductRepository $productRepository,
        LikeRepository $likeRepository,
        DislikeRepository $dislikeRepository,
        EntityManagerInterface $entityManagerInterface
    ) {

        $product = $productRepository->find($id);
        $user = $this->getUser();

        if (!$user) {
            return $this->json(
                [
                    'code' => 403,
                    'message' => "Vous devez vous connecter"
                ],
                403
            );
        }

        if ($product->isLikeByUser($user)) {
            $like = $likeRepository->findOneBy(
                [
                    'product' => $product,
                    'user' => $user
                ]
            );

            $entityManagerInterface->remove($like);
            $entityManagerInterface->flush();

            return $this->json([
                'code' => 200,
                'message' => "Like supprimé",
                'likes' => $likeRepository->count(['product' => $product]),
                'dislikes' => $dislikeRepository->count(['product' => $product])
            ], 200);
        }

        $like = new Like();

        $like->setProduct($product);
        $like->setUser($user);

        $entityManagerInterface->persist($like);
        $entityManagerInterface->flush();

        if ($product->isDislikeByUser($user)) {
            $dislike = $dislikeRepository->findOneBy(['product' => $product]);
            $entityManagerInterface->remove($dislike);
            $entityManagerInterface->flush();
        }

        return $this->json([
            'code' => 200,
            'message' => "Like ajouté",
            'likes' => $likeRepository->count(['product' => $product]),
            'dislikes' => $dislikeRepository->count(['product' => $product])
        ], 200);
    }

    #[Route('/dislike/product/{id}', name: 'product_dislike')]
    public function dislikeProduct(
        $id,
        ProductRepository $productRepository,
        DislikeRepository $dislikeRepository,
        LikeRepository $likeRepository,
        EntityManagerInterface $entityManagerInterface
    ) {

        $product = $productRepository->find($id);
        $user = $this->getUser();

        if (!$user) {
            return $this->json(
                [
                    'code' => 403,
                    'message' => "Vous devez vous connecter"
                ],
                403
            );
        }

        if ($product->isDislikeByUser($user)) {
            $dislike = $dislikeRepository->findOneBy(
                [
                    'product' => $product,
                    'user' => $user
                ]
            );

            $entityManagerInterface->remove($dislike);
            $entityManagerInterface->flush();

            return $this->json([
                'code' => 200,
                'message' => "Like supprimé",
                'likes' => $likeRepository->count(['product' => $product]),
                'dislikes' => $dislikeRepository->count(['product' => $product])
            ], 200);
        }

        $dislike = new Dislike();

        $dislike->setProduct($product);
        $dislike->setUser($user);

        $entityManagerInterface->persist($dislike);
        $entityManagerInterface->flush();

        if ($product->isLikeByUser($user)) {
            $like = $likeRepository->findOneBy(['product' => $product]);
            $entityManagerInterface->remove($like);
            $entityManagerInterface->flush();
        }

        return $this->json([
            'code' => 200,
            'message' => "Like ajouté",
            'dislikes' => $dislikeRepository->count(['product' => $product]),
            'likes' => $likeRepository->count(['product' => $product])
        ], 200);
    }
}
