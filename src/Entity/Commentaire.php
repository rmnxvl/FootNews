<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Article;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')] // On utilise "text" pour les commentaires longs
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')] // Type date/heure pour enregistrer la date de création
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)] // Relation avec l'utilisateur (auteur du commentaire)
    #[ORM\JoinColumn(nullable: false)] // Impossible d'avoir un commentaire sans auteur
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'commentaires')] // Relation avec l'article
    #[ORM\JoinColumn(nullable: false)] // Impossible d'avoir un commentaire sans article
    private ?Article $article = null;

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }
}