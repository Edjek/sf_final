<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_user_list')]
    public function userList(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this->render('admin/admin_user/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, MailerInterface $mailerInterface): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_USER"]);
            $user->setDate(new \DateTime("NOW"));
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user_mail = $form->get('email')->getData();
            $user_name = $form->get('lastname')->getData();
            $user_firstname = $form->get('firstname')->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $email = (new TemplatedEmail())
                ->from('admin@gmail.com')
                ->to($user_mail)
                ->subject('Mise à jour de votre compte')
                ->htmlTemplate('front/mail/email.html.twig')
                ->context([
                    'name' => $user_name,
                    'firstname' => $user_firstname,
                ]);
            $mailerInterface->send($email);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/update/user/', name: 'update_user')]
    public function updateRegister(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        MailerInterface $mailerInterface
    ): Response {
        $form = $this->createForm(RegistrationFormType::class,  $this->getUser());

        $form->handleRequest($request);

        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setDate(new \DateTime("NOW"));
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $to    = $form->get('email')->getData();
            $email = (new Email())
                ->from('admin@gmail.com')
                ->to($to)
                ->subject('Mise à jour de votre compte')
                ->html('<h1>Bien joué! Vous avez mis à jour votre compte.</h1>');
            $mailerInterface->send($email);

            $this->addFlash(
                'notice',
                'Votre compte a été mis à jour'
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/user/', name: 'delete_user')]
    public function deleteUser(UserRepository $userRepository, EntityManagerInterface $entityManagerInterface)
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        $entityManagerInterface->remove($user);

        $entityManagerInterface->flush();

        $session = new Session();
        $session->invalidate();


        $this->addFlash(
            'notice',
            'Le compte a été supprimé'
        );
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
