<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240606195647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE factor_users ADD location_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE factor_users ADD CONSTRAINT FK_E73AF01664D218E FOREIGN KEY (location_id) REFERENCES locations (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E73AF01664D218E ON factor_users (location_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE factor_users DROP FOREIGN KEY FK_E73AF01664D218E');
        $this->addSql('DROP INDEX IDX_E73AF01664D218E ON factor_users');
        $this->addSql('ALTER TABLE factor_users DROP location_id');
    }
}
