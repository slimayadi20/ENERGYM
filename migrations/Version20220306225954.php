<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220306225954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit_user (produit_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A40BABA6F347EFB (produit_id), INDEX IDX_A40BABA6A76ED395 (user_id), PRIMARY KEY(produit_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_user ADD CONSTRAINT FK_A40BABA6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_user ADD CONSTRAINT FK_A40BABA6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation DROP INDEX IDX_AB55E24F79F37AE5, ADD UNIQUE INDEX UNIQ_AB55E24F79F37AE5 (id_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE produit_user');
        $this->addSql('ALTER TABLE participation DROP INDEX UNIQ_AB55E24F79F37AE5, ADD INDEX IDX_AB55E24F79F37AE5 (id_user_id)');
    }
}
