<?php

namespace App\Tests;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        // Création d'un article
        $article = new Article();
        

        $article->setTitre("Titre");
        $article->setContenu("Contenu");
        $article->setCategorie("Categorie");


        $this->assertSame("Titre", $article->getTitre());
        $this->assertSame("Contenu", $article->getContenu());
        $this->assertSame("Categorie", $article->getCategorie());
    }
}