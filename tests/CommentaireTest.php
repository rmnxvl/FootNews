<?php

namespace App\Tests\Entity;

use App\Entity\Commentaire;
use App\Entity\User;
use App\Entity\Article;
use PHPUnit\Framework\TestCase;

class CommentaireTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Création d'un commentaire
        $commentaire = new Commentaire();

        // --- Test du content ---
        $expectedContent = 'Ceci est le contenu du commentaire';
        $commentaire->setContent($expectedContent);
        $this->assertSame($expectedContent, $commentaire->getContent());

        // --- Test du createdAt ---
        // On utilise DateTimeImmutable pour correspondre à la signature de setCreatedAt
        $expectedDate = new \DateTimeImmutable('2025-01-01 12:34:56');
        $commentaire->setCreatedAt($expectedDate);
        $this->assertSame($expectedDate, $commentaire->getCreatedAt());

        // --- Test de l'author (relation avec User) ---
        // Au lieu de créer un User réel, on crée un mock si vous ne souhaitez pas tester la classe User ici
        $mockedUser = $this->createMock(User::class);
        $commentaire->setAuthor($mockedUser);
        $this->assertSame($mockedUser, $commentaire->getAuthor());

        // --- Test de l'article (relation avec Article) ---
        // Même logique : on crée un mock de l'article
        $mockedArticle = $this->createMock(Article::class);
        $commentaire->setArticle($mockedArticle);
        $this->assertSame($mockedArticle, $commentaire->getArticle());
    }
}
