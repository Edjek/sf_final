<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminImageController extends AbstractController
{
    #[Route('/admin/image', name: 'admin_image')]
    public function index(): Response
    {
        return $this->render('admin_image/index.html.twig', [
            'controller_name' => 'AdminImageController',
        ]);
    }
}
