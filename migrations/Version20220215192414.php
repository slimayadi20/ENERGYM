<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220215192414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, salleassocie_id INT NOT NULL, nom VARCHAR(255) NOT NULL, nom_coach VARCHAR(255) NOT NULL, nombre INT NOT NULL, description VARCHAR(500) NOT NULL, image VARCHAR(255) NOT NULL, date DATE NOT NULL, heure_d TIME NOT NULL, heure_f TIME NOT NULL, INDEX IDX_FDCA8C9C6E22ECC1 (salleassocie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(500) NOT NULL, tel BIGINT NOT NULL, mail VARCHAR(250) NOT NULL, description VARCHAR(500) NOT NULL, prix INT NOT NULL, heureo TIME NOT NULL, heuref TIME NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C6E22ECC1 FOREIGN KEY (salleassocie_id) REFERENCES salle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C6E22ECC1');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE salle');
    }
}
