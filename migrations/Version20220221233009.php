<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220221233009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_event_id INT NOT NULL, INDEX IDX_AB55E24F79F37AE5 (id_user_id), INDEX IDX_AB55E24F212C041E (id_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F212C041E FOREIGN KEY (id_event_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E31338A73 FOREIGN KEY (nom_categorie_id) REFERENCES categories_event (id)');
        $this->addSql('CREATE INDEX IDX_B26681E31338A73 ON evenement (nom_categorie_id)');
        $this->addSql('ALTER TABLE user CHANGE status status INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE participation');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E31338A73');
        $this->addSql('DROP INDEX IDX_B26681E31338A73 ON evenement');
        $this->addSql('ALTER TABLE user CHANGE status status TINYINT(1) NOT NULL');
    }
}
