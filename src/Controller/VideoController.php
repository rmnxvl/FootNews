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

#[Route('/admin/video')]
#[IsGranted('ROLE_ADMIN')] // Restreint l'accès aux admins
class VideoController extends AbstractController
{
    #[Route('/', name: 'admin_video_index', methods: ['GET', 'POST'])]
    public function index(VideoRepository $videoRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les vidéos pour l'affichage
        $videos = $videoRepository->findBy([], ['createdAt' => 'DESC']);

        // Gérer l'ajout d'une nouvelle vidéo (formulaire)
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

        // Gérer la modification (édition) d'une vidéo existante
        $editId = $request->query->get('edit'); // Récupère l'ID de la vidéo à modifier
        $videoToEdit = null;
        $editForm = null;

        if ($editId) {
            $videoToEdit = $videoRepository->find($editId);
            if (!$videoToEdit) {
                throw $this->createNotFoundException('Vidéo non trouvée.');
            }
            $editForm = $this->createForm(VideoType::class, $videoToEdit);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Vidéo modifiée avec succès.');
                return $this->redirectToRoute('admin_video_index');
            }
        }

        // Gérer la suppression
        $deleteId = $request->query->get('delete');
        if ($deleteId) {
            $videoToDelete = $videoRepository->find($deleteId);
            if ($videoToDelete) {
                if ($this->isCsrfTokenValid('delete' . $videoToDelete->getId(), $request->query->get('_token'))) {
                    try {
                        $entityManager->remove($videoToDelete);
                        $entityManager->flush();
                        $this->addFlash('success', 'Vidéo supprimée avec succès.');
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
                    }
                } else {
                    $this->addFlash('error', 'Token CSRF invalide.');
                }
                return $this->redirectToRoute('admin_video_index');
            }
        }

        // Passer editId au template, même s'il est null
        return $this->render('video/index.html.twig', [
            'videos' => $videos,
            'form' => $form->createView(),
            'editForm' => $editForm ? $editForm->createView() : null,
            'editId' => $editId, // Ajout explicite de editId
        ]);
    }

    // Tu peux conserver les autres méthodes (show) si elles sont utiles, ou les supprimer si tout est centralisé dans index
}