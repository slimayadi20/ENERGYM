<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220215184237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) DEFAULT NULL, methode_paiement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livraison (id INT AUTO_INCREMENT NOT NULL, id_commande_id INT NOT NULL, nom_livreur VARCHAR(255) NOT NULL, date_livraison DATE NOT NULL, etat VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A60C9F1F9AF8E3A3 (id_commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, contenu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F9AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL, ADD roles VARCHAR(255) NOT NULL, ADD status TINYINT(1) NOT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD image_file VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F9AF8E3A3');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE livraison');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('ALTER TABLE user DROP password, DROP roles, DROP status, DROP created_at, DROP email, DROP image_file');
    }
}
