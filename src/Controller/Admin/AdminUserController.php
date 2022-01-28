<?php

namespace App\Controller\Admin;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserController extends AbstractController
{
    #[Route('/admin/user', name: 'admin_user_list')]
    public function userList(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/admin_user/users.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/update/user/{id}', name: 'update_user_by_id')]
    public function updateRegisterById(
        $id,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $userRepository->find($id);
        $form = $this->createForm(RegistrationFormType::class,  $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le compte a été mis à jour'
            );

            return $this->redirectToRoute("admin_main");
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/user/{id}', name: 'delete_user_by_id')]
    public function deleteUserById($id, UserRepository $userRepository, EntityManagerInterface $entityManagerInterface)
    {
        $user = $userRepository->find($id);

        $entityManagerInterface->remove($user);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'Le compte a été supprimé'
        );

        return $this->redirectToRoute("admin_main");
    }
}
