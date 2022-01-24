<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminLicenceController extends AbstractController
{
    #[Route('/admin/licence', name: 'admin_licence')]
    public function index(): Response
    {
        return $this->render('admin_licence/index.html.twig', [
            'controller_name' => 'AdminLicenceController',
        ]);
    }
}
