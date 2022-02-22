<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220222211103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(150) NOT NULL, date_creation VARCHAR(35) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, article_id INT DEFAULT NULL, date_creation VARCHAR(255) NOT NULL, contenu VARCHAR(255) NOT NULL, INDEX IDX_67F068BC7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE reclamation ADD nom_user_id INT DEFAULT NULL, ADD statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640452E1D85D FOREIGN KEY (nom_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE60640452E1D85D ON reclamation (nom_user_id)');
        $this->addSql('ALTER TABLE user CHANGE status status INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC7294869C');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640452E1D85D');
        $this->addSql('DROP INDEX IDX_CE60640452E1D85D ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP nom_user_id, DROP statut');
        $this->addSql('ALTER TABLE user CHANGE status status TINYINT(1) NOT NULL');
    }
}
