<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403194642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE air_conditionings (id INT AUTO_INCREMENT NOT NULL, unit_id INT NOT NULL, sector_id INT NOT NULL, category VARCHAR(255) NOT NULL, INDEX IDX_CB01F067F8BD700D (unit_id), INDEX IDX_CB01F067DE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE air_conditionings ADD CONSTRAINT FK_CB01F067F8BD700D FOREIGN KEY (unit_id) REFERENCES units (id)');
        $this->addSql('ALTER TABLE air_conditionings ADD CONSTRAINT FK_CB01F067DE95C867 FOREIGN KEY (sector_id) REFERENCES sectors (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE air_conditionings DROP FOREIGN KEY FK_CB01F067F8BD700D');
        $this->addSql('ALTER TABLE air_conditionings DROP FOREIGN KEY FK_CB01F067DE95C867');
        $this->addSql('DROP TABLE air_conditionings');
    }
}
