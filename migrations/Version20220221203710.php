<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220221203710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation ADD statut VARCHAR(255) NOT NULL, CHANGE nom_user_id nom_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640452E1D85D FOREIGN KEY (nom_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CE60640452E1D85D ON reclamation (nom_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640452E1D85D');
        $this->addSql('DROP INDEX IDX_CE60640452E1D85D ON reclamation');
        $this->addSql('ALTER TABLE reclamation DROP statut, CHANGE nom_user_id nom_user_id INT NOT NULL');
    }
}
