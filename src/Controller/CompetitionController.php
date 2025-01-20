<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompetitionController extends AbstractController
{
    // Afficher toutes les compétitions avec une option de recherche par pays
    #[Route('/competitions', name: 'competition_index', methods: ['GET'])]
    public function index(Request $request, CompetitionRepository $competitionRepository): Response
    {
        $country = $request->query->get('country', '');

        if (!empty($country)) {
            $competitions = $competitionRepository->findBy(['countryName' => $country]);
        } else {
            $competitions = $competitionRepository->findAll();
        }

        return $this->render('competitions/list.html.twig', [
            'competitions' => $competitions,
            'country' => $country,
        ]);
    }

    // Afficher les détails d'une compétition
    #[Route('/competitions/{id<\d+>}', name: 'competition_show', methods: ['GET'])]
    public function show(Competition $competition): Response
    {
        return $this->render('competitions/show.html.twig', [
            'competition' => $competition,
        ]);
    }

    // Créer une nouvelle compétition (réservé aux administrateurs)
    #[Route('/competitions/create', name: 'admin_competition_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $countryName = $request->request->get('countryName');
            $competitionLogo = $request->request->get('competitionLogo');
            $countryLogo = $request->request->get('countryLogo');

            if (!empty($title) && !empty($countryName)) {
                $competition = new Competition();
                $competition->setTitle($title);
                $competition->setCountryName($countryName);
                $competition->setCompetitionLogo($competitionLogo);
                $competition->setCountryLogo($countryLogo);

                $em->persist($competition);
                $em->flush();

                return $this->redirectToRoute('competition_index');
            }
        }

        return $this->render('competitions/create.html.twig');
    }


    // Modifier une compétition existante (réservé aux administrateurs)
    #[Route('/competitions/{id<\d+>}/edit', name: 'competition_edit', methods: ['GET', 'POST'])]
    public function edit(Competition $competition, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $countryName = $request->request->get('countryName');
            $competitionLogo = $request->request->get('competitionLogo');
            $countryLogo = $request->request->get('countryLogo');

            if (!empty($title) && !empty($countryName)) {
                $competition->setTitle($title);
                $competition->setCountryName($countryName);
                $competition->setCompetitionLogo($competitionLogo);
                $competition->setCountryLogo($countryLogo);

                $em->flush();

                return $this->redirectToRoute('competition_index');
            }
        }

        return $this->render('competitions/edit.html.twig', [
            'competition' => $competition,
        ]);
    }

    // Supprimer une compétition (réservé aux administrateurs)
    #[Route('/competitions/{id<\d+>}/delete', name: 'competition_delete', methods: ['POST'])]
    public function delete(Competition $competition, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($competition);
        $em->flush();

        return $this->redirectToRoute('competition_index');
    }
}