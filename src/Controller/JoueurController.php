<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Joueur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/joueurs')]
class JoueurController extends AbstractController
{
    #[Route('/', name: 'crud_joueurs_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): Response
    {
        $joueurs = $em->getRepository(Joueur::class)->findAll();

        return $this->render('admin/joueurs/index.html.twig', [
            'joueurs' => $joueurs,
        ]);
    }

    #[Route('/edit/{id}', name: 'crud_joueurs_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Joueur $joueur,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        // Création du formulaire avec un champ d'upload
        $form = $this->createFormBuilder($joueur)
            ->add('nom_complet', TextType::class, ['label' => 'Nom complet'])
            ->add('age', IntegerType::class, ['label' => 'Âge'])
            ->add('pays', TextType::class, ['label' => 'Pays'])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo du joueur (fichier PNG/JPG)',
                'mapped' => false,
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de la nouvelle image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                // Génération d'un nom unique pour le fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('player_images_directory'), // Répertoire défini dans services.yaml
                        $newFilename
                    );

                    // Suppression de l'ancienne image si elle existe
                    if ($joueur->getImage()) {
                        $oldImagePath = $this->getParameter('player_images_directory') . '/' . $joueur->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    // Mise à jour de l'image dans l'entité
                    $joueur->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de l\'image.');
                }
            }

            $em->flush();

            $this->addFlash('success', 'Joueur modifié avec succès.');

            return $this->redirectToRoute('crud_joueurs_index');
        }

        return $this->render('admin/joueurs/edit.html.twig', [
            'form' => $form->createView(),
            'joueur' => $joueur,
        ]);
    }

    #[Route('/delete/{id}', name: 'crud_joueurs_delete', methods: ['POST'])]
    public function delete(Joueur $joueur, EntityManagerInterface $em): Response
    {
        // Suppression de l'image associée
        if ($joueur->getImage()) {
            $imagePath = $this->getParameter('player_images_directory') . '/' . $joueur->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $em->remove($joueur);
        $em->flush();

        $this->addFlash('success', 'Joueur supprimé avec succès.');

        return $this->redirectToRoute('crud_joueurs_index');
    }
}