<?php

namespace App\Controller\Front;

use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImageController extends AbstractController
{
    #[Route('/images', name: 'image_list')]
    public function imageList(ImageRepository $imageRepository): Response
    {
        $images = $imageRepository->findAll();

        return $this->render('front/image/images.html.twig', [
            'images' => $images,
        ]);
    }

    #[Route('/image/{id}', name: 'image_show')]
    public function imageShow($id, ImageRepository $imageRepository): Response
    {
        $image = $imageRepository->find($id);

        return $this->render('front/image/image.html.twig', [
            'image' => $image,
        ]);
    }
}
