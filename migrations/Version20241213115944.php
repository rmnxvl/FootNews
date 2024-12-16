<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241213115944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire ADD author_id INT NOT NULL, ADD article_id INT NOT NULL, ADD content LONGTEXT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP contenu');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_67F068BCF675F31B ON commentaire (author_id)');
        $this->addSql('CREATE INDEX IDX_67F068BC7294869C ON commentaire (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCF675F31B');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('DROP INDEX IDX_67F068BCF675F31B ON commentaire');
        $this->addSql('DROP INDEX IDX_67F068BC7294869C ON commentaire');
        $this->addSql('ALTER TABLE commentaire ADD contenu VARCHAR(255) NOT NULL, DROP author_id, DROP article_id, DROP content, DROP created_at');
    }
}
