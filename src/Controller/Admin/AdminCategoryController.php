<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'admin_category_list')]
    public function categoryList(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('admin/admin_category/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/create/category', name: 'admin_create_category')]
    public function createCategory(EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Une categorie a été créé'
            );

            return $this->redirectToRoute("admin_category_list");
        }

        return $this->render("admin/admin_category/categoryform.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }

    #[Route('/admin/update/category/{id}', name: 'admin_update_category')]
    public function updateCategory(
        $id,
        CategoryRepository $categoryRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {

        $category = $categoryRepository->find($id);

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'La categorie a été modifié'
            );

            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render("admin/admin_category/categoryform.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }

    #[Route('/admin/delete/category/{id}', name: 'admin_delete_category')]
    public function deleteCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface)
    {
        $category = $categoryRepository->find($id);

        $entityManagerInterface->remove($category);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'La categorie a été supprimé'
        );

        return $this->redirectToRoute("admin_category_list");
    }
}
