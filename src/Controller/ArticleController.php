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
    #[Route('/articles', name: 'article_index', methods: ['GET'])]
    public function index(Request $request, ArticleRepository $articleRepository): Response
    {
        $categorie = $request->query->get('categorie', '');

        $articles = $categorie 
            ? $articleRepository->findBy(['categorie' => $categorie]) 
            : $articleRepository->findAll();

        return $this->render('articles/liste.html.twig', [
            'articles' => $articles,
            'categorie' => $categorie,
        ]);
    }

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

    #[Route('/articles/{id}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
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

    #[Route('/articles/{id}/delete', name: 'article_delete', methods: ['POST'])]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article_index');
    }

    #[Route('/articles/{id}', name: 'article_show', methods: ['GET', 'POST'])]
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
    
    #[Route('/articles/categorie/{categorie}', name: 'article_by_category', methods: ['GET'])]
    public function articlesByCategory(ArticleRepository $articleRepository, string $categorie): Response
    {
        $articles = $articleRepository->findBy(['categorie' => $categorie]);

        return $this->render('articles/liste.html.twig', [
            'articles' => $articles,
            'categorie' => $categorie,
        ]);
    }
}