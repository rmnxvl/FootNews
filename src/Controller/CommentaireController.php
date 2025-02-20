<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentaireController extends AbstractController
{
    #[Route('/commentaires', name: 'commentaire_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $commentaires = $entityManager->getRepository(Commentaire::class)->findAll();

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/commentaire/new/{articleId}', name: 'commentaire_new', methods: ['POST'])]
    public function nouveau(Request $request, EntityManagerInterface $entityManager, int $articleId): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($articleId);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé.');
        }

        $commentaire = new Commentaire();
        $commentaire->setAuthor($this->getUser());
        $commentaire->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        $commentaire->setContenu($request->request->get('contenu'));
        $commentaire->setArticle($article);

        $entityManager->persist($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('article_show', ['id' => $articleId]);
    }

    #[Route('/articles/{id}', name: 'article_show', methods: ['GET'])]
    public function show(Article $article, EntityManagerInterface $entityManager): Response
    {
        $commentaires = $entityManager->getRepository(Commentaire::class)->findBy(['article' => $article]);
        $commentForms = [];

        foreach ($commentaires as $commentaire) {
            $commentForms[$commentaire->getId()] = $this->createForm(CommentaireType::class, $commentaire, [
                'action' => $this->generateUrl('commentaire_edit', ['id' => $commentaire->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        return $this->render('articles/voirarticles.html.twig', [
            'article' => $article,
            'commentaires' => $commentaires,
            'commentForms' => $commentForms, // Passe les formulaires au template
        ]);
    }

    #[Route('/commentaire/{id}/delete', name: 'commentaire_delete', methods: ['POST'])]
    public function supprimer(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($commentaire->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Vous n\'avez pas le droit de supprimer ce commentaire.');
        }

        $articleId = $commentaire->getArticle()->getId();

        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_show', ['id' => $articleId]);
    }

    #[Route('/commentaire/{id}/edit', name: 'commentaire_edit', methods: ['POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($commentaire->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Vous n\'avez pas le droit de modifier ce commentaire.');
        }

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('article_show', ['id' => $commentaire->getArticle()->getId()]);
        }

        return $this->render('commentaire/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
