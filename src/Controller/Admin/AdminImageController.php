<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminImageController extends AbstractController
{
    #[Route('/admin/image', name: 'admin_image_list')]
    public function imageList(ImageRepository $imageRepository)
    {
        $images = $imageRepository->findAll();

        return $this->render('admin/admin_image/images.html.twig', [
            'images' => $images,
        ]);
    }

    #[Route('/admin/create/image', name: 'admin_create_image')]
    public function createImage(EntityManagerInterface $entityManagerInterface, Request $request, SluggerInterface $sluggerInterface)
    {
        $image = new Image();
        $imageForm = $this->createForm(ImageType::class, $image);

        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            // On récupère le fichier
            $imageFile = $imageForm->get('src')->getData();

            if ($imageFile) {

                // On créée un nom unique à notre fichier à partir du nom original
                // Pour éviter tout problème de confusion

                // On récupère le nom original du fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pour avoir un nom valide du fichier
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // On déplace le fichier dans le dossier public/image
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $image->setSrc($newFilename);
            }
            $entityManagerInterface->persist($image);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Une image a été créée'
            );

            return $this->redirectToRoute("admin_image_list");
        }

        return $this->render("admin/admin_image/imageform.html.twig", ['imageForm' => $imageForm->createView()]);
    }

    #[Route('/admin/update/image/{id}', name: 'admin_update_image')]
    public function updateImage(
        $id,
        ImageRepository $imageRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface
    ) {

        $image = $imageRepository->find($id);

        $imageForm = $this->createForm(ImageType::class, $image);

        $imageForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            // On récupère le fichier
            $imageFile = $imageForm->get('src')->getData();

            if ($imageFile) {

                // On créée un nom unique à notre fichier à partir du nom original
                // Pour éviter tout problème de confusion

                // On récupère le nom original du fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pour avoir un nom valide du fichier
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // On déplace le fichier dans le dossier public/image
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $image->setSrc($newFilename);
            }
            $entityManagerInterface->persist($image);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'L\'image a été modifiée'
            );

            return $this->redirectToRoute('admin_image_list');
        }

        return $this->render("admin/admin_image/imageform.html.twig", ['imageForm' => $imageForm->createView()]);
    }

    #[Route('/admin/delete/image/{id}', name: 'admin_delete_image')]
    public function deleteImage($id, ImageRepository $imageRepository, EntityManagerInterface $entityManagerInterface)
    {
        $image = $imageRepository->find($id);

        $entityManagerInterface->remove($image);

        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'L\'image a été supprimée'
        );

        return $this->redirectToRoute("admin_image_list");
    }
}
