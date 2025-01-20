<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleWebTest extends WebTestCase
{
    // Vérifie que la page des articles est accessible
    public function testArticlePageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/articles');

        $this->assertResponseIsSuccessful();  // 200 OK
        $this->assertPageTitleContains('Articles');  // Titre de la page
    }

    // Vérifie qu'un article spécifique est accessible
    public function testArticleContentIsVisible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/articles/1');

        $this->assertResponseIsSuccessful();  // La page doit s'afficher sans erreur
        $this->assertStringContainsString('articles', $client->getRequest()->getUri());  // Vérifie l'URL
        $this->assertGreaterThan(0, strlen($client->getResponse()->getContent()));  // Vérifie que la page a du contenu
    }

    // Vérifie qu'un article inexistant retourne bien une erreur 404
    public function testNonExistentArticleReturns404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/articles/999');

        $this->assertResponseStatusCodeSame(404);  // La page doit renvoyer une erreur 404
        $this->assertStringContainsString('Page non trouvée', $client->getResponse()->getContent());  // Message d'erreur personnalisé
    }
}