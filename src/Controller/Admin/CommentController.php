<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\CommentAdminType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/admin/comment', name: 'admin_comment_list')]
    public function commentList(CommentRepository $commentRepository)
    {
        $comments = $commentRepository->findAll();

        return $this->render('admin/admin_comment/comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/admin/comment/create', name: 'admin_create_comment')]
    public function createComment(EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $comment = new Comment();

        $commentForm = $this->createForm(CommentAdminType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Un commentaire a été créé'
            );

            return $this->redirectToRoute("admin_comment_list");
        }

        return $this->render("admin/admin_comment/commentform.html.twig", ['commentForm' => $commentForm->createView()]);
    }

    #[Route('/admin/comment/update/{id}', name: 'admin_update_comment')]
    public function updateComment(
        $id,
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {

        $comment = $commentRepository->find($id);

        $commentForm = $this->createForm(CommentAdminType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Le commentaire a été modifié'
            );

            return $this->redirectToRoute('admin_comment_list');
        }

        return $this->render("admin/admin_comment/commentform.html.twig", ['commentForm' => $commentForm->createView()]);
    }

    #[Route('/admin/comment/delete/{id}', name: 'admin_delete_comment')]
    public function deleteComment($id, CommentRepository $commentRepository, EntityManagerInterface $entityManagerInterface)
    {
        $comment = $commentRepository->find($id);

        $entityManagerInterface->remove($comment);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'Le commentaire a été supprimé'
        );

        return $this->redirectToRoute("admin_comment_list");
    }
}
