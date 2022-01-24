<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMainController extends AbstractController
{
    #[Route('/admin/main', name: 'admin_main')]
    public function index(): Response
    {
        return $this->render('admin_main/index.html.twig', [
            'controller_name' => 'AdminMainController',
        ]);
    }
}
