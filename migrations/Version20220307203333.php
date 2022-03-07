<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307203333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE participation DROP INDEX IDX_AB55E24F79F37AE5, ADD UNIQUE INDEX UNIQ_AB55E24F79F37AE5 (id_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP created_at');
        $this->addSql('ALTER TABLE participation DROP INDEX UNIQ_AB55E24F79F37AE5, ADD INDEX IDX_AB55E24F79F37AE5 (id_user_id)');
    }
}
