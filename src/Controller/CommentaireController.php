<?php

namespace App\Controller;

use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaireController extends AbstractController
{
    #[Route('/commentaire/{id}/edit', name: 'commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Commentaire $commentaire, Request $request, EntityManagerInterface $em): Response
    {
        // Autoriser l'admin ou l'auteur du commentaire à modifier
        if ($this->getUser() !== $commentaire->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez modifier que vos propres commentaires.');
            return $this->redirectToRoute('article_index');
        }

        if ($request->isMethod('POST')) {
            $contenu = $request->request->get('content');
            if (!empty($contenu)) {
                $commentaire->setContent($contenu);
                $em->flush();

                $this->addFlash('success', 'Le commentaire a été modifié avec succès.');
                return $this->redirectToRoute('article_show', ['id' => $commentaire->getArticle()->getId()]);
            } else {
                $this->addFlash('error', 'Le commentaire ne peut pas être vide.');
            }
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/commentaire/{id}/delete', name: 'commentaire_delete', methods: ['POST'])]
    public function delete(Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        // Autoriser l'admin ou l'auteur du commentaire à supprimer
        if ($this->getUser() !== $commentaire->getAuthor() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez supprimer que vos propres commentaires.');
            return $this->redirectToRoute('article_index');
        }

        $articleId = $commentaire->getArticle()->getId();
        $em->remove($commentaire);
        $em->flush();

        $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
        return $this->redirectToRoute('article_show', ['id' => $articleId]);
    }
}