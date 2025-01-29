<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(
        Request $request,
        ArticleRepository $articleRepository,
        EquipeRepository $equipeRepository,
        JoueurRepository $joueurRepository
    ): JsonResponse {
        $query = $request->query->get('q', ''); // Texte saisi par l'utilisateur
        $results = []; // Tableau des résultats

        if (!empty($query)) {
            // 🔍 Rechercher des articles
            $articles = $articleRepository->searchArticles($query);
            foreach ($articles as $article) {
                $results[] = [
                    'type' => 'article',
                    'id' => $article->getId(),
                    'title' => $article->getTitre(),
                    'content' => mb_substr($article->getContenu(), 0, 100) . '...',
                    'image' => $article->getImage() ? '/uploads/articles/' . $article->getImage() : null,
                ];
            }

            // 🔍 Rechercher des équipes
            $equipes = $equipeRepository->searchTeams($query);
            foreach ($equipes as $equipe) {
                $results[] = [
                    'type' => 'team',
                    'id' => $equipe->getId(),
                    'name' => $equipe->getNom(), // Nom de l'équipe
                    'country' => $equipe->getPays(), // Pays
                    'logo' => $equipe->getLogo() ? '/uploads/teams/' . $equipe->getLogo() : null,
                ];
            }

            // 🔍 Rechercher des joueurs
            $joueurs = $joueurRepository->searchPlayers($query);
            foreach ($joueurs as $joueur) {
                $results[] = [
                    'type' => 'player',
                    'id' => $joueur->getId(),
                    'name' => $joueur->getNomComplet(), // Nom complet
                    'age' => $joueur->getAge(), // Âge
                    'team' => $joueur->getEquipe() ? $joueur->getEquipe()->getNom() : null, // Équipe associée
                    'country' => $joueur->getPays(), // Pays
                    'image' => $joueur->getImage() ? '/uploads/players/' . $joueur->getImage() : null,
                ];
            }
        }

        return new JsonResponse(['results' => $results]); // Renvoie tous les résultats
    }
}