<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
    #[Route('/admin/product', name: 'admin_product_list')]
    public function productList(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('admin/admin_product/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/admin/product/create', name: 'admin_create_product')]
    public function createProduct(EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $product = new Product();

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Une product a été créé'
            );

            return $this->redirectToRoute("admin_product_list");
        }

        return $this->render("admin/admin_product/productform.html.twig", ['productForm' => $productForm->createView()]);
    }

    #[Route('/admin/product/update/{id}', name: 'admin_update_product')]
    public function updateProduct(
        $id,
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {

        $product = $productRepository->find($id);

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'La product a été modifié'
            );

            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render("admin/admin_product/productform.html.twig", ['productForm' => $productForm->createView()]);
    }

    #[Route('/admin/product/delete/{id}', name: 'admin_delete_product')]
    public function deleteProduct($id, ProductRepository $productRepository, EntityManagerInterface $entityManagerInterface)
    {
        $product = $productRepository->find($id);

        $entityManagerInterface->remove($product);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'La product a été supprimé'
        );

        return $this->redirectToRoute("admin_product_list");
    }
}
