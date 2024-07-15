<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240406220839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE factor_users (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, factor_fqcn VARCHAR(255) NOT NULL, factor_id INT NOT NULL, amount NUMERIC(22, 12) DEFAULT NULL, gas_activity VARCHAR(255) DEFAULT NULL, INDEX IDX_E73AF016A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE factor_users ADD CONSTRAINT FK_E73AF016A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE factor_users DROP FOREIGN KEY FK_E73AF016A76ED395');
        $this->addSql('DROP TABLE factor_users');
    }
}
