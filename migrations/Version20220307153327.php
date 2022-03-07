<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307153327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_salle_id INT NOT NULL, UNIQUE INDEX UNIQ_5E90F6D679F37AE5 (id_user_id), INDEX IDX_5E90F6D68CEBACA0 (id_salle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D679F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D68CEBACA0 FOREIGN KEY (id_salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE commande CHANGE methode_paiement methode_paiement VARCHAR(255) NOT NULL, CHANGE adresse adresse VARCHAR(255) NOT NULL, CHANGE tel tel BIGINT NOT NULL');
        $this->addSql('ALTER TABLE participation DROP INDEX IDX_AB55E24F79F37AE5, ADD UNIQUE INDEX UNIQ_AB55E24F79F37AE5 (id_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE inscription');
        $this->addSql('ALTER TABLE commande CHANGE methode_paiement methode_paiement VARCHAR(255) DEFAULT NULL, CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE tel tel BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE participation DROP INDEX UNIQ_AB55E24F79F37AE5, ADD INDEX IDX_AB55E24F79F37AE5 (id_user_id)');
    }
}
