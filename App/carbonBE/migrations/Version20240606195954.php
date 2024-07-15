<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606195954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE locations ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE locations ADD CONSTRAINT FK_17E64ABAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_17E64ABAA76ED395 ON locations (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE locations DROP FOREIGN KEY FK_17E64ABAA76ED395');
        $this->addSql('DROP INDEX IDX_17E64ABAA76ED395 ON locations');
        $this->addSql('ALTER TABLE locations DROP user_id');
    }
}
