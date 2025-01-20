<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipeController extends AbstractController
{
    // Afficher toutes les équipes avec une option de recherche par localisation
    #[Route('/equipes', name: 'equipe_index', methods: ['GET'])]
    public function index(Request $request, EquipeRepository $equipesRepository): Response
    {
        $localisation = $request->query->get('localisation', '');

        if (!empty($localisation)) {
            $equipes = $equipesRepository->findBy(['localisation' => $localisation]);
        } else {
            $equipes = $equipesRepository->findAll();
        }

        return $this->render('equipes/list.html.twig', [
            'equipes' => $equipes,
            'localisation' => $localisation,
        ]);
    }

    // Afficher les détails d'une équipe
    #[Route('/equipes/{id<\d+>}', name: 'equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipes/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    // Créer une nouvelle équipe (réservé aux administrateurs)
    #[Route('/equipes/create', name: 'equipe_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if ($request->isMethod('POST')) {
            $titreEquipe = $request->request->get('titreEquipe');
            $localisation = $request->request->get('localisation');
            
            // Validation des champs
            if (!empty($titreEquipe)) {
                $equipe = new Equipe();
                $equipe->setTitreEquipe($titreEquipe);
                $equipe->setLocalisation($localisation);
                
                $em->persist($equipe);
                $em->flush();
    
                // Redirection après la création
                return $this->redirectToRoute('equipe_index');
            }
        }
    
        return $this->render('equipes/create.html.twig');
    }

    // Modifier une équipe existante (réservé aux administrateurs)
    #[Route('/equipes/{id<\d+>}/edit', name: 'equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Equipe $equipe, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $titreEquipe = $request->request->get('titreEquipe');
            $idUtilisateurs = $request->request->get('idUtilisateurs');
            $logoEquipe = $request->request->get('logoEquipe');
            $localisation = $request->request->get('localisation');

            if (!empty($titreEquipe) && !empty($idUtilisateurs)) {
                $equipe->setTitreEquipe($titreEquipe);
                $equipe->setLogoEquipe($logoEquipe);
                $equipe->setLocalisation($localisation);

                $em->flush();

                return $this->redirectToRoute('equipe_index');
            }
        }

        return $this->render('equipes/edit.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    // Supprimer une équipe (réservé aux administrateurs)
    #[Route('/equipes/{id<\d+>}/delete', name: 'equipe_delete', methods: ['POST'])]
    public function delete(Equipe $equipe, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($equipe);
        $em->flush();

        return $this->redirectToRoute('equipe_index');
    }
}