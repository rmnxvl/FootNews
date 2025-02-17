<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Article;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:read'])] // ✅ Inclure l'ID dans l'API
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['comment:read'])] // ✅ Inclure le contenu dans l'API
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['comment:read'])] // ✅ Inclure la date de création
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read'])] // ✅ On inclut seulement `username` et `profilePicture`
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article = null; // ❌ PAS de Groups -> Non exposé

    // ✅ Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }
}