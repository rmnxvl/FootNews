<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipePublicController extends AbstractController
{
    #[Route('/psg', name: 'psg_show', methods: ['GET'])]
    public function psgShow(EquipeRepository $equipesRepository): Response
    {
        // Récupérer l'équipe Paris Saint-Germain par son nom
        $equipe = $equipesRepository->findOneBy(['nom' => 'Paris-Saint-Germain']);
    
        if (!$equipe) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }
    
        return $this->render('/equipes/psg.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/real-madrid', name: 'real_madrid_show', methods: ['GET'])]
    public function realMadridShow(EquipeRepository $equipesRepository): Response
    {
        $equipe = $equipesRepository->findOneBy(['nom' => 'Real Madrid']);
    
        if (!$equipe) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }
    
        return $this->render('/equipes/real_madrid.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/juventus', name: 'juventus_show', methods: ['GET'])]
    public function juventusShow(EquipeRepository $equipesRepository): Response
    {
        $equipe = $equipesRepository->findOneBy(['nom' => 'Juventus']);
    
        if (!$equipe) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }
    
        return $this->render('/equipes/juventus.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/manchester-united', name: 'manchester_united_show', methods: ['GET'])]
    public function manchesterUnitedShow(EquipeRepository $equipesRepository): Response
    {
        $equipe = $equipesRepository->findOneBy(['nom' => 'Manchester United']);
    
        if (!$equipe) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }
    
        return $this->render('/equipes/manchester_united.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    // Continuez ce schéma pour chaque équipe

    #[Route('/dinamo-zagreb', name: 'dinamo_zagreb_show', methods: ['GET'])]
    public function dinamoZagrebShow(EquipeRepository $equipesRepository): Response
    {
        $equipe = $equipesRepository->findOneBy(['nom' => 'Dinamo Zagreb']);
    
        if (!$equipe) {
            throw $this->createNotFoundException('Équipe non trouvée');
        }
    
        return $this->render('/equipes/dinamo_zagreb.html.twig', [
            'equipe' => $equipe,
        ]);
    }
}