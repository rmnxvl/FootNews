<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\VideoRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, VideoRepository $videoRepository): Response
    {
        // Récupération des articles triés par date décroissante
        $articles = $articleRepository->findAllOrderedByDateDesc();

        // Récupération des vidéos triées par date décroissante
        $videos = $videoRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('home/home.html.twig', [
            'articles' => $articles,
            'videos' => $videos, 
        ]);
    }
}