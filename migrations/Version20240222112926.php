<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222112926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE crypto_currency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, current_price DOUBLE PRECISION NOT NULL, daily_prices JSON NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wallet DROP INDEX FK_7C68921FA76ED395, ADD UNIQUE INDEX UNIQ_7C68921FA76ED395 (user_id)');
        $this->addSql('ALTER TABLE wallet CHANGE solde solde DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE crypto_currency');
        $this->addSql('ALTER TABLE wallet DROP INDEX UNIQ_7C68921FA76ED395, ADD INDEX FK_7C68921FA76ED395 (user_id)');
        $this->addSql('ALTER TABLE wallet CHANGE solde solde DOUBLE PRECISION NOT NULL');
    }
}
