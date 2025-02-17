<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/comments')]
class ApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'get_comments', methods: ['GET'])]
    public function getComments(CommentaireRepository $commentaireRepository, SerializerInterface $serializer): JsonResponse
    {
        $comments = $commentaireRepository->findAll();
        
        // ✅ Appliquer les groupes de sérialisation
        $json = $serializer->serialize($comments, 'json', ['groups' => 'comment:read']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}/approve', name: 'approve_comment', methods: ['POST'])]
    public function approveComment($id, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $comment = $commentaireRepository->find($id);

        if (!$comment) {
            return $this->json(['message' => 'Commentaire introuvable'], 404);
        }

        $comment->setApproved(true);
        $this->entityManager->flush();

        return $this->json(['message' => 'Commentaire approuvé']);
    }

    #[Route('/{id}/edit', name: 'edit_comment', methods: ['PUT'])]
    public function editComment($id, Request $request, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $comment = $commentaireRepository->find($id);

        if (!$comment) {
            return $this->json(['message' => 'Commentaire introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['content']) && !empty($data['content'])) {
            $comment->setContent($data['content']);
            $this->entityManager->flush();

            return $this->json(['message' => 'Commentaire modifié']);
        }

        return $this->json(['message' => 'Le contenu ne peut pas être vide'], 400);
    }

    #[Route('/{id}/delete', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment($id, CommentaireRepository $commentaireRepository): JsonResponse
    {
        $comment = $commentaireRepository->find($id);

        if (!$comment) {
            return $this->json(['message' => 'Commentaire introuvable'], 404);
        }

        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        return $this->json(['message' => 'Commentaire supprimé']);
    }
}