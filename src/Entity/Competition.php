<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Titre de la compétition
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // Nom du pays de la compétition
    #[ORM\Column(length: 255)]
    private ?string $countryName = null;

    // Logo de la compétition
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $competitionLogo = null;

    // Logo du pays
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $countryLogo = null;

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function setCountryName(string $countryName): self
    {
        $this->countryName = $countryName;

        return $this;
    }

    public function getCompetitionLogo(): ?string
    {
        return $this->competitionLogo;
    }

    public function setCompetitionLogo(?string $competitionLogo): self
    {
        $this->competitionLogo = $competitionLogo;

        return $this;
    }

    public function getCountryLogo(): ?string
    {
        return $this->countryLogo;
    }

    public function setCountryLogo(?string $countryLogo): self
    {
        $this->countryLogo = $countryLogo;

        return $this;
    }
}