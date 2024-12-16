<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Articles = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticles(): ?string
    {
        return $this->Articles;
    }

    public function setArticles(string $Articles): static
    {
        $this->Articles = $Articles;

        return $this;
    }
}
