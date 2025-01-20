<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin/users', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Bloque l'accès si l'utilisateur n'est pas admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        $users = $userRepository->findAll();
    
        return $this->render('/admin/user/liste.html.twig', ['users' => $users]);
    }

    // ✅ Route modifiée pour afficher /profil
    #[Route('/profil', name: 'user_profil', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profil(): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();
    
        return $this->render('user/profil.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
public function edit(User $user, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    // 🔒 Vérifie si l'utilisateur connecté peut modifier ce compte
    if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
        $this->addFlash('error', 'Vous ne pouvez modifier que vos propres informations.');
        return $this->redirectToRoute('user_index');
    }

    if ($request->isMethod('POST')) {
        // ✅ Mise à jour des informations de base
        $user->setUsername($request->request->get('username'));
        $user->setEmail($request->request->get('email'));

        // ✅ Gestion de l'upload de la photo de profil
        $uploadedFile = $request->files->get('profile_picture');
        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            try {
                // 📂 Déplace le fichier dans le dossier uploads
                $uploadedFile->move(
                    $this->getParameter('profile_pictures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                return $this->redirectToRoute('user_profil');
            }

            // 🔄 Met à jour la photo de profil dans la base de données
            $user->setProfilePicture($newFilename);
        }

        // ✅ Empêche les utilisateurs non-admins de changer leur rôle
        if ($this->isGranted('ROLE_ADMIN')) {
            $newRole = $request->request->get('role');

            if ($newRole) {
                // 📌 Récupère les rôles existants
                $currentRoles = $user->getRoles();

                // 🔄 Si le rôle n'existe pas déjà, on l'ajoute
                if (!in_array($newRole, $currentRoles, true)) {
                    $currentRoles[] = $newRole;
                }

                // ✅ Met à jour les rôles sans supprimer les rôles existants
                $user->setRoles($currentRoles);
            }
        }

        // ✅ Sauvegarde en base de données
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Les informations ont été mises à jour avec succès.');
        return $this->redirectToRoute('user_profil');
    }

    return $this->render('/admin/user/edit.html.twig', ['user' => $user]);
}

    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        // Vérifiez si l'utilisateur connecté est soit l'utilisateur en question, soit un administrateur
        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez supprimer que votre propre compte.');
            return $this->redirectToRoute('user_index');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Compte supprimé avec succès.');
        return $this->redirectToRoute('user_index');
    }
}