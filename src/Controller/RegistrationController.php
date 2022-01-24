<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_USER"]);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

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
            // dump($user);die;
            // encode the plain password

            $session = new Session();
            $session->start();

            // set and get session attributes
            $session->set('email', $this->getUser()->getUserIdentifier());
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
}
