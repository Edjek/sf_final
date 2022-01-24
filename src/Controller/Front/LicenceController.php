<?php

namespace App\Controller\Front;

use App\Repository\LicenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LicenceController extends AbstractController
{
    #[Route('/licences', name: 'licence_list')]
    public function licenceList(LicenceRepository $licenceRepository): Response
    {
        $licences = $licenceRepository->findAll();

        return $this->render('front/licence/licences.html.twig', [
            'licences' => $licences,
        ]);
    }

    #[Route('/licence/{id}', name: 'licence_show')]
    public function licenceShow($id, LicenceRepository $licenceRepository): Response
    {
        $licence = $licenceRepository->find($id);

        return $this->render('front/licence/licence.html.twig', [
            'licence' => $licence,
        ]);
    }
}
