<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titreEquipe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoEquipe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localisation = null;

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreEquipe(): ?string
    {
        return $this->titreEquipe;
    }

    public function setTitreEquipe(string $titreEquipe): self
    {
        $this->titreEquipe = $titreEquipe;

        return $this;
    }

    public function getLogoEquipe(): ?string
    {
        return $this->logoEquipe;
    }

    public function setLogoEquipe(?string $logoEquipe): self
    {
        $this->logoEquipe = $logoEquipe;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }
}