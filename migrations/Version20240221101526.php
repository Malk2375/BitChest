<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221101526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wallet DROP INDEX FK_7C68921FA76ED395, ADD UNIQUE INDEX UNIQ_7C68921FA76ED395 (user_id)');
        $this->addSql('ALTER TABLE wallet CHANGE user_id user_id INT DEFAULT NULL, CHANGE solde solde DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wallet DROP INDEX UNIQ_7C68921FA76ED395, ADD INDEX FK_7C68921FA76ED395 (user_id)');
        $this->addSql('ALTER TABLE wallet CHANGE user_id user_id INT NOT NULL, CHANGE solde solde DOUBLE PRECISION NOT NULL');
    }
}
