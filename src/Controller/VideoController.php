<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur pour gérer les opérations CRUD sur les vidéos.
 */
#[Route('/admin/video', name: 'admin_video_')]
#[IsGranted('ROLE_ADMIN')] // Restreint l'accès aux utilisateurs avec le rôle ADMIN
class VideoController extends AbstractController
{
    /**
     * Affiche la liste des vidéos et gère la suppression.
     */
    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        VideoRepository $videoRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer toutes les vidéos, triées par date de création décroissante
        $videos = $videoRepository->findBy([], ['createdAt' => 'DESC']);

        // Gérer la suppression d'une vidéo
        $deleteId = $request->query->get('delete');
        if ($deleteId) {
            $videoToDelete = $videoRepository->find($deleteId);
            if ($videoToDelete) {
                if ($this->isCsrfTokenValid('delete_video', $request->request->get('_token'))) {
                    try {
                        $entityManager->remove($videoToDelete);
                        $entityManager->flush();
                        $this->addFlash('success', 'Vidéo supprimée avec succès.');
                    } catch (\Exception $e) {
                        $this->addFlash('error', sprintf('Erreur lors de la suppression : %s', $e->getMessage()));
                    }
                } else {
                    $this->addFlash('error', 'Token CSRF invalide. Veuillez réessayer.');
                }
                return $this->redirectToRoute('admin_video_index');
            }
            $this->addFlash('error', 'La vidéo à supprimer n\'existe pas.');
            return $this->redirectToRoute('admin_video_index');
        }

        // Rendre la page de liste
        return $this->render('/admin/video/index.html.twig', [
            'videos' => $videos,
        ]);
    }

    /**
     * Affiche et gère la création d'une nouvelle vidéo.
     */
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($video);
            $entityManager->flush();

            $this->addFlash('success', 'Vidéo ajoutée avec succès.');
            return $this->redirectToRoute('admin_video_index');
        }

        return $this->render('/admin/video/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche et gère la modification d'une vidéo existante.
     */
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        VideoRepository $videoRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $video = $videoRepository->find($id);
        if (!$video) {
            throw $this->createNotFoundException('La vidéo demandée n\'existe pas.');
        }

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Vidéo modifiée avec succès.');
            return $this->redirectToRoute('admin_video_index');
        }

        return $this->render('/admin/video/edit.html.twig', [
            'form' => $form->createView(),
            'video' => $video,
        ]);
    }
}