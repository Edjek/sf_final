<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LicenceController extends AbstractController
{
    #[Route('/licence', name: 'licence')]
    public function index(): Response
    {
        return $this->render('licence/index.html.twig', [
            'controller_name' => 'LicenceController',
        ]);
    }
}
