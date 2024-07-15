<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403182956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wastes DROP INDEX UNIQ_F843B2BDDE95C867, ADD INDEX IDX_F843B2BDDE95C867 (sector_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wastes DROP INDEX IDX_F843B2BDDE95C867, ADD UNIQUE INDEX UNIQ_F843B2BDDE95C867 (sector_id)');
    }
}
