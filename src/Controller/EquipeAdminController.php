<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/equipes')]
class EquipeAdminController extends AbstractController
{
    // Liste des équipes avec option de recherche
    #[Route('/', name: 'admin_equipe_index', methods: ['GET'])]
    public function index(Request $request, EquipeRepository $equipesRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pays = $request->query->get('pays', '');
        $equipes = !empty($pays) ? $equipesRepository->findBy(['pays' => $pays]) : $equipesRepository->findAll();

        return $this->render('admin/equipes/list.html.twig', [
            'equipes' => $equipes,
            'pays' => $pays,
        ]);
    }

    // Affichage des détails d'une équipe
    #[Route('/{id<\d+>}', name: 'admin_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('/equipes/psg.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    // Création d'une équipe
    #[Route('/create', name: 'admin_equipe_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $pays = $request->request->get('pays');
            $logo = $request->files->get('logo');

            if (!empty($nom)) {
                $equipe = new Equipe();
                $equipe->setNom($nom);
                $equipe->setPays($pays);

                // Gestion de l'upload du logo
                if ($logo) {
                    $nomFichier = uniqid() . '.' . $logo->guessExtension();
                    $logo->move($this->getParameter('logos_directory'), $nomFichier);
                    $equipe->setLogo('/uploads/logos/' . $nomFichier);
                }

                $em->persist($equipe);
                $em->flush();

                return $this->redirectToRoute('admin_equipe_index');
            }
        }

        return $this->render('admin/equipes/create.html.twig');
    }

    // Modification d'une équipe
    #[Route('/{id<\d+>}/edit', name: 'admin_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Equipe $equipe, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $pays = $request->request->get('pays');
            $logo = $request->files->get('logo');

            if (!empty($nom)) {
                $equipe->setNom($nom);
                $equipe->setPays($pays);

                // Gestion de l'upload du nouveau logo
                if ($logo) {
                    $nomFichier = uniqid() . '.' . $logo->guessExtension();
                    $logo->move($this->getParameter('logos_directory'), $nomFichier);
                    $equipe->setLogo('/uploads/logos/' . $nomFichier);
                }

                $em->flush();

                return $this->redirectToRoute('admin_equipe_index');
            }
        }

        return $this->render('admin/equipes/edit.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    // Suppression d'une équipe
    #[Route('/{id<\d+>}/delete', name: 'admin_equipe_delete', methods: ['POST'])]
    public function delete(Equipe $equipe, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($equipe);
        $em->flush();

        return $this->redirectToRoute('admin_equipe_index');
    }
}