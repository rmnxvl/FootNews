<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class ArticleTest extends TestCase
{
    private Article $article;

    protected function setUp(): void
    {
        // Initialisation avant chaque test
        $this->article = new Article();
    }

    public function testInitialState(): void
    {
        // Vérifie l'état initial de l'objet
        $this->assertNull($this->article->getId());
        $this->assertNull($this->article->getTitre());
        $this->assertNull($this->article->getContenu());
        $this->assertNull($this->article->getImage());
        $this->assertNull($this->article->getImageFile());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->article->getCreatedAt());
        $this->assertNull($this->article->getUpdatedAt());
    }

    public function testSetAndGetTitre(): void
    {
        // Test du setter et getter pour titre
        $titre = "Mon Article";
        $this->article->setTitre($titre);
        $this->assertEquals($titre, $this->article->getTitre());
    }

    public function testSetAndGetContenu(): void
    {
        // Test du setter et getter pour contenu
        $contenu = "Ceci est le contenu de mon article.";
        $this->article->setContenu($contenu);
        $this->assertEquals($contenu, $this->article->getContenu());
    }

    public function testSetAndGetImage(): void
    {
        // Test du setter et getter pour image
        $image = "image.jpg";
        $this->article->setImage($image);
        $this->assertEquals($image, $this->article->getImage());
    }

    public function testSetImageFileUpdatesUpdatedAt(): void
    {
        // Test que setImageFile met à jour updatedAt
        $file = $this->createMock(File::class); // Mock d'un objet File
        $this->article->setImageFile($file);
        
        $this->assertSame($file, $this->article->getImageFile());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->article->getUpdatedAt());
        $this->assertGreaterThan(
            $this->article->getCreatedAt(),
            $this->article->getUpdatedAt()
        );
    }

    public function testSetImageFileToNullDoesNotUpdateUpdatedAt(): void
    {
        // Test que setImageFile(null) ne modifie pas updatedAt
        $this->article->setImageFile(null);
        $this->assertNull($this->article->getImageFile());
        $this->assertNull($this->article->getUpdatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        // Test du setter et getter pour updatedAt
        $updatedAt = new \DateTimeImmutable('2023-01-01');
        $this->article->setUpdatedAt($updatedAt);
        $this->assertSame($updatedAt, $this->article->getUpdatedAt());
    }

    protected function tearDown(): void
    {
        // Nettoyage après chaque test
        unset($this->article);
    }
}