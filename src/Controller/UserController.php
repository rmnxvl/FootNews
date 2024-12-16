<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Seul les administrateurs peuvent voir tous les utilisateurs
        if ($this->isGranted('ROLE_ADMIN')) {
            $users = $userRepository->findAll();
        } else {
            // Les utilisateurs non-admins voient uniquement leurs propres informations
            $users = [$this->getUser()];
        }

        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Seuls les administrateurs peuvent créer de nouveaux utilisateurs
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $user = new User();

            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));

            $hashedPassword = password_hash($request->request->get('password'), PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);

            $role = $request->request->get('role', 'ROLE_USER');
            $user->setRoles([$role]);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig');
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // Vérifiez si l'utilisateur connecté est soit l'utilisateur en question, soit un administrateur
        if ($this->getUser() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres informations.');
            return $this->redirectToRoute('user_index');
        }

        if ($request->isMethod('POST')) {
            $user->setName($request->request->get('name'));
            $user->setEmail($request->request->get('email'));

            // Empêcher les utilisateurs non-admins de changer leur rôle
            if ($this->isGranted('ROLE_ADMIN')) {
                $role = $request->request->get('role', 'ROLE_USER');
                $user->setRoles([$role]);
            }

            $em->flush();

            $this->addFlash('success', 'Les informations ont été mises à jour avec succès.');
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', ['user' => $user]);
    }

    #[Route('/{id}/delete', name: 'user_delete', methods: ['POST'])]
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