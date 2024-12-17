<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    // Afficher les articles avec filtrage par catégorie et recherche par mot-clé
    #[Route('/articles', name: 'article_index', methods: ['GET'])]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $categorie = $request->query->get('categorie', '');
        $search = $request->query->get('search', '');

        $articles = $articleRepository->searchArticles($search, $categorie);

        return $this->render('articles/liste.html.twig', [
            'articles' => $articles,
            'categorie' => $categorie,
        ]);
    }

    // Voir un article avec ses commentaires et ajouter un commentaire
    #[Route('/articles/{id<\d+>}', name: 'article_show', methods: ['GET', 'POST'])]
    public function show(
        Article $article,
        Request $request,
        EntityManagerInterface $em,
        CommentaireRepository $commentaireRepository
    ): Response {
        $comments = $commentaireRepository->findBy(['article' => $article]);

        if ($request->isMethod('POST') && $this->getUser()) {
            $content = $request->request->get('content');

            if (!empty($content)) {
                $comment = new Commentaire();
                $comment->setContent($content);
                $comment->setAuthor($this->getUser());
                $comment->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $comment->setArticle($article);

                $em->persist($comment);
                $em->flush();

                return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
            }
        }

        return $this->render('articles/voirarticles.html.twig', [
            'article' => $article,
            'comments' => $comments,
        ]);
    }

    // Créer un article (réservé aux administrateurs)
    #[Route('/articles/create', name: 'article_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre');
            $categorie = $request->request->get('categorie');
            $contenu = $request->request->get('contenu');

            if (!empty($titre) && !empty($categorie) && !empty($contenu)) {
                $article = new Article();
                $article->setTitre($titre);
                $article->setCategorie($categorie);
                $article->setContenu($contenu);

                $em->persist($article);
                $em->flush();

                return $this->redirectToRoute('article_index');
            }
        }

        return $this->render('articles/create.html.twig');
    }

    // Modifier un article existant (réservé aux administrateurs)
    #[Route('/articles/{id<\d+>}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre');
            $categorie = $request->request->get('categorie');
            $contenu = $request->request->get('contenu');

            if (!empty($titre) && !empty($categorie) && !empty($contenu)) {
                $article->setTitre($titre);
                $article->setCategorie($categorie);
                $article->setContenu($contenu);

                $em->flush();

                return $this->redirectToRoute('article_index');
            }
        }

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
        ]);
    }

    // Supprimer un article (réservé aux administrateurs)
    #[Route('/articles/{id<\d+>}/delete', name: 'article_delete', methods: ['POST'])]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article_index');
    }
}