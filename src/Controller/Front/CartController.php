<?php

namespace App\Controller\Front;

use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'show_cart')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $this->render('front/cart/cart.html.twig', [
            'cartProducts' => $cartWithData
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_cart')]
    public function addCard($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('product_show', [
            'id' => $id,
        ]);
    }

    #[Route('/cart/delete/{id}', name: 'delete_cart')]
    public function deleteCart($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id]) && $cart[$id] == 1) {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('show_cart');
    }

    #[Route('/cart/infos', name: 'cart_info')]
    public function carInfos(UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if ($user) {
            $user_mail = $user->getUserIdentifier();
            $user_true = $userRepository->findOneBy(['email', $user_mail]);
            return $this->render('front/cart/cartinfo.html.twig', ['user'=>$user]);
        } else {
            return $this->render('front/cart/cartinfo.html.twig');
        }
    }
}
