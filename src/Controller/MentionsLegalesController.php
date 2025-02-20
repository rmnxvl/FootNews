<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MentionsLegalesController extends AbstractController
{
    #[Route('/conditions', name: 'app_conditions')]
    public function conditions(): Response
    {
        return $this->render('conditions.html.twig', [
            'contact_email' => 'contact@footnews.com',
            'last_updated' => new \DateTime(),
        ]);
    }

    #[Route('/politiqueconfidentialite', name: 'app_politique_confidentialite')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('politiqueconfidentialite.html.twig', [
            'contact_email' => 'contact@footnews.com',
            'last_updated' => new \DateTime(),
        ]);
    }
}