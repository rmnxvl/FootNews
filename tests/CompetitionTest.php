<?php

namespace App\Tests\Entity;

use App\Entity\Competition;
use PHPUnit\Framework\TestCase;

class CompetitionTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Instanciation de l'entité à tester
        $competition = new Competition();

        // Définition des valeurs de test
        $title = 'Champions League';
        $countryName = 'Europe';
        $competitionLogo = 'champions_league_logo.png';
        $countryLogo = 'europe_logo.png';

        // On teste les setters
        $competition->setTitle($title);
        $competition->setCountryName($countryName);
        $competition->setCompetitionLogo($competitionLogo);
        $competition->setCountryLogo($countryLogo);

        // On vérifie les getters
        $this->assertSame($title, $competition->getTitle());
        $this->assertSame($countryName, $competition->getCountryName());
        $this->assertSame($competitionLogo, $competition->getCompetitionLogo());
        $this->assertSame($countryLogo, $competition->getCountryLogo());
    }
}
