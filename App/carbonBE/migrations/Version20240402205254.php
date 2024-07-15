<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402205254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE factor_gases (id INT AUTO_INCREMENT NOT NULL, gas_id INT NOT NULL, factor_fqcn VARCHAR(255) NOT NULL, factor_id INT NOT NULL, value NUMERIC(12, 8) DEFAULT NULL, INDEX IDX_1A22F0B4E0EBD3EC (gas_id), UNIQUE INDEX factor_gas_UNIQUE (factor_fqcn, factor_id, gas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gases (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, formula VARCHAR(255) NOT NULL, activity VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sectors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokens (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token_key VARCHAR(255) NOT NULL, refresh_token_key VARCHAR(255) NOT NULL, expires_at DATETIME DEFAULT NULL, refresh_expires_at DATETIME DEFAULT NULL, last_active_date DATETIME NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_AA5A118EA76ED395 (user_id), UNIQUE INDEX tokenkey_UNIQUE (token_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE units (id INT AUTO_INCREMENT NOT NULL, measuring_unit VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT 0 NOT NULL, login_token VARCHAR(255) DEFAULT NULL, password_reset_token VARCHAR(255) DEFAULT NULL, password_reset_token_expires_at DATETIME DEFAULT NULL, activation_token VARCHAR(255) DEFAULT NULL, activation_token_expires_at DATETIME DEFAULT NULL, email_confirmed_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wastes (id INT AUTO_INCREMENT NOT NULL, unit_id INT NOT NULL, sector_id INT NOT NULL, category VARCHAR(255) NOT NULL, INDEX IDX_F843B2BDF8BD700D (unit_id), UNIQUE INDEX UNIQ_F843B2BDDE95C867 (sector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE factor_gases ADD CONSTRAINT FK_1A22F0B4E0EBD3EC FOREIGN KEY (gas_id) REFERENCES gases (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tokens ADD CONSTRAINT FK_AA5A118EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wastes ADD CONSTRAINT FK_F843B2BDF8BD700D FOREIGN KEY (unit_id) REFERENCES units (id)');
        $this->addSql('ALTER TABLE wastes ADD CONSTRAINT FK_F843B2BDDE95C867 FOREIGN KEY (sector_id) REFERENCES sectors (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE factor_gases DROP FOREIGN KEY FK_1A22F0B4E0EBD3EC');
        $this->addSql('ALTER TABLE tokens DROP FOREIGN KEY FK_AA5A118EA76ED395');
        $this->addSql('ALTER TABLE wastes DROP FOREIGN KEY FK_F843B2BDF8BD700D');
        $this->addSql('ALTER TABLE wastes DROP FOREIGN KEY FK_F843B2BDDE95C867');
        $this->addSql('DROP TABLE factor_gases');
        $this->addSql('DROP TABLE gases');
        $this->addSql('DROP TABLE sectors');
        $this->addSql('DROP TABLE tokens');
        $this->addSql('DROP TABLE units');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE wastes');
    }
}
