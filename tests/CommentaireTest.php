<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentaireTest extends TestCase
{
    private Commentaire $commentaire;

    // Méthode exécutée avant chaque test
    protected function setUp(): void
    {
        $this->commentaire = new Commentaire();
    }

    public function testInitialValues(): void
    {
        // Vérifie que les valeurs initiales sont null
        $this->assertNull($this->commentaire->getId());
        $this->assertNull($this->commentaire->getContenu());
        $this->assertNull($this->commentaire->getCreatedAt());
        $this->assertNull($this->commentaire->getAuthor());
        $this->assertNull($this->commentaire->getArticle());
    }

    public function testSetAndGetContenu(): void
    {
        $contenu = "Ceci est un commentaire de test.";
        $this->commentaire->setContenu($contenu);

        $this->assertEquals($contenu, $this->commentaire->getContenu());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $createdAt = new \DateTimeImmutable('2023-10-15 12:00:00');
        $this->commentaire->setCreatedAt($createdAt);

        $this->assertSame($createdAt, $this->commentaire->getCreatedAt());
    }

    public function testSetAndGetAuthor(): void
    {
        $author = new User(); // Supposons que User existe
        $this->commentaire->setAuthor($author);

        $this->assertSame($author, $this->commentaire->getAuthor());
    }

    public function testSetAndGetArticle(): void
    {
        $article = new Article(); // Supposons que Article existe
        $this->commentaire->setArticle($article);

        $this->assertSame($article, $this->commentaire->getArticle());
    }

    public function testChainingSetters(): void
    {
        $contenu = "Commentaire avec chaînage";
        $createdAt = new \DateTimeImmutable();
        $author = new User();
        $article = new Article();

        $this->commentaire
            ->setContenu($contenu)
            ->setCreatedAt($createdAt)
            ->setAuthor($author)
            ->setArticle($article);

        $this->assertEquals($contenu, $this->commentaire->getContenu());
        $this->assertSame($createdAt, $this->commentaire->getCreatedAt());
        $this->assertSame($author, $this->commentaire->getAuthor());
        $this->assertSame($article, $this->commentaire->getArticle());
    }
}