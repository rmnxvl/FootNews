<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    // 📄 Afficher la liste des articles (publique)
    #[Route('/admin/articles', name: 'article_index', methods: ['GET'])]
public function index(Request $request, ArticleRepository $articleRepository): Response
{
    $search = $request->query->get('search');  // Récupère la recherche dans l'URL
    $articles = $articleRepository->searchArticles($search);  // Utilise la recherche

    return $this->render('admin/articles/liste.html.twig', [
        'articles' => $articles,
    ]);
}

    // 📄 Afficher un article et ses commentaires (publique)
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

    // ✅ Créer un nouvel article (ADMIN uniquement)
    #[Route('/admin/articles/create', name: 'admin_article_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article créé avec succès !');

            return $this->redirectToRoute('article_index');
        }

        return $this->render('admin/articles/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // ✏️ Modifier un article existant (ADMIN uniquement)
    #[Route('/admin/articles/{id<\d+>}/edit', name: 'admin_article_edit', methods: ['GET', 'POST'])]
    public function edit(Article $article, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre');
            $contenu = $request->request->get('contenu');
            $imageFile = $request->files->get('imageFile');
    
            if ($titre && $contenu) {
                $article->setTitre($titre);
                $article->setContenu($contenu);
    
                // ⚡️ Gestion de l'upload de l'image
                if ($imageFile) {
                    $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/articles';
                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();
    
                    try {
                        $imageFile->move($uploadsDir, $newFilename);
                        $article->setImage($newFilename);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                        return $this->redirectToRoute('admin_article_edit', ['id' => $article->getId()]);
                    }
                }
    
                $em->flush();
                $this->addFlash('success', 'Article mis à jour avec succès !');
    
                return $this->redirectToRoute('article_index');
            } else {
                $this->addFlash('error', 'Veuillez remplir tous les champs.');
            }
        }
    
        return $this->render('admin/articles/edit.html.twig', [
            'article' => $article,
        ]);
    }

    // 🗑️ Supprimer un article (ADMIN uniquement)
    #[Route('/admin/articles/{id<\d+>}/delete', name: 'admin_article_delete', methods: ['POST'])]
    public function delete(Article $article, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($article);
        $em->flush();

        $this->addFlash('success', 'Article supprimé !');

        return $this->redirectToRoute('article_index');
    }
}