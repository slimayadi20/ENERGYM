<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220222101444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories_event (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) DEFAULT NULL, methode_paiement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, salleassocie_id INT NOT NULL, nom VARCHAR(255) NOT NULL, nom_coach VARCHAR(255) NOT NULL, nombre INT NOT NULL, description VARCHAR(500) NOT NULL, image VARCHAR(255) NOT NULL, date DATE NOT NULL, heure_d TIME NOT NULL, heure_f TIME NOT NULL, INDEX IDX_FDCA8C9C6E22ECC1 (salleassocie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, nom_event VARCHAR(255) NOT NULL, date_event VARCHAR(255) NOT NULL, description_event VARCHAR(1000) NOT NULL, lieu_event VARCHAR(1000) NOT NULL, nbr_participants_event VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livraison (id INT AUTO_INCREMENT NOT NULL, id_commande_id INT NOT NULL, nom_livreur VARCHAR(255) NOT NULL, date_livraison DATE NOT NULL, etat VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A60C9F1F9AF8E3A3 (id_commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, categories_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(1000) NOT NULL, prix INT NOT NULL, quantite INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, nom_user_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, contenu VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_CE60640452E1D85D (nom_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(500) NOT NULL, tel BIGINT NOT NULL, mail VARCHAR(250) NOT NULL, description VARCHAR(500) NOT NULL, prix INT NOT NULL, heureo TIME NOT NULL, heuref TIME NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C6E22ECC1 FOREIGN KEY (salleassocie_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F9AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640452E1D85D FOREIGN KEY (nom_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL, ADD roles VARCHAR(255) NOT NULL, ADD status INT NOT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD image_file VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A21214B7');
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F9AF8E3A3');
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C6E22ECC1');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_event');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE livraison');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE salle');
        $this->addSql('ALTER TABLE user DROP password, DROP roles, DROP status, DROP created_at, DROP email, DROP image_file');
    }
}
