<?php

namespace App\Controller\Front;

use App\Form\ChangePasswordType;
use Symfony\Component\Mime\Email;
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

class AccountController extends AbstractController
{
    #[Route('/compte/update/', name: 'update_account')]
    public function updateRegister(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        MailerInterface $mailerInterface
    ): Response {
        $form = $this->createForm(ChangePasswordType::class,  $this->getUser());

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

        return $this->render('front/account/updateaccountform.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/compte/delete/', name: 'delete_account')]
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
