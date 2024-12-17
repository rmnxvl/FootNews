<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    #[Route('/articles/recherche', name: 'article_search', methods: ['GET'])]
    public function search(Request $request, ArticleRepository $articleRepository): Response
    {
        // Récupère le mot-clé de la requête
        $search = $request->query->get('search', '');

        // Recherche des articles en fonction du mot-clé
        $articles = $articleRepository->searchArticles($search, null);

        // Retourne la vue avec les articles trouvés
        return $this->render('articles/liste.html.twig', [
            'articles' => $articles,
            'categorie' => null,
            'search' => $search,
        ]);
    }
}