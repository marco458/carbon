<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403212256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE freight_transportations (id INT AUTO_INCREMENT NOT NULL, unit_id INT NOT NULL, sector_id INT NOT NULL, vehicle_type VARCHAR(255) NOT NULL, fuel_and_load VARCHAR(255) NOT NULL, euro_standard VARCHAR(255) NOT NULL, INDEX IDX_6DAD9394F8BD700D (unit_id), INDEX IDX_6DAD9394DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE freight_transportations ADD CONSTRAINT FK_6DAD9394F8BD700D FOREIGN KEY (unit_id) REFERENCES units (id)');
        $this->addSql('ALTER TABLE freight_transportations ADD CONSTRAINT FK_6DAD9394DE95C867 FOREIGN KEY (sector_id) REFERENCES sectors (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE freight_transportations DROP FOREIGN KEY FK_6DAD9394F8BD700D');
        $this->addSql('ALTER TABLE freight_transportations DROP FOREIGN KEY FK_6DAD9394DE95C867');
        $this->addSql('DROP TABLE freight_transportations');
    }
}
