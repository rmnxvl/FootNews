<?php

namespace App\Tests\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ArticleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function testIndexPageLoadsCorrectly(): void
    {
        $this->client->request('GET', '/admin/articles');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des articles');
    }

    public function testShowArticlePage(): void
    {
        // Vérifier s'il y a un article en BDD, sinon en créer un
        $article = $this->entityManager->getRepository(Article::class)->findOneBy([]);

        if (!$article) {
            $article = new Article();
            $article->setTitre('Article Test');
            $article->setContenu('Ceci est un test');

            $this->entityManager->persist($article);
            $this->entityManager->flush();
        }

        $this->client->request('GET', '/articles/' . $article->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.article-title'); // Vérifie que le titre est bien affiché
    }

    public function testAddCommentRequiresAuthentication(): void
    {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy([]);

        if (!$article) {
            $this->markTestSkipped('Aucun article trouvé.');
        }

        // Essayer de poster un commentaire sans être connecté
        $this->client->request('POST', '/articles/' . $article->getId(), [
            'content' => 'Ceci est un test de commentaire'
        ]);

        $this->assertResponseRedirects('/login'); // Vérifie que l'utilisateur est redirigé vers la connexion
    }
}
