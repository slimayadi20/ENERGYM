<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220318125459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE chat_entity');
        $this->addSql('ALTER TABLE reclamation CHANGE produit produit VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat_entity (id INT AUTO_INCREMENT NOT NULL, id_sender_id INT DEFAULT NULL, id_receiver_id INT DEFAULT NULL, message VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_69F4734976110FBA (id_sender_id), UNIQUE INDEX UNIQ_69F47349D5412041 (id_receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE chat_entity ADD CONSTRAINT FK_69F4734976110FBA FOREIGN KEY (id_sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chat_entity ADD CONSTRAINT FK_69F47349D5412041 FOREIGN KEY (id_receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation CHANGE produit produit VARCHAR(255) NOT NULL');
    }
}
