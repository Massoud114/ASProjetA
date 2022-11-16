<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113055934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD fixed_price DOUBLE PRECISION DEFAULT NULL, ADD weight_unit VARCHAR(255) DEFAULT NULL, ADD brand VARCHAR(255) DEFAULT NULL, ADD thickness DOUBLE PRECISION DEFAULT NULL, ADD thickness_unit VARCHAR(255) DEFAULT NULL, ADD width DOUBLE PRECISION DEFAULT NULL, ADD width_unit VARCHAR(255) DEFAULT NULL, ADD height DOUBLE PRECISION DEFAULT NULL, ADD height_unit VARCHAR(255) DEFAULT NULL, ADD length DOUBLE PRECISION DEFAULT NULL, ADD length_unit VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD estimated_make_at DATETIME DEFAULT NULL, CHANGE state status INT NOT NULL');
        $this->addSql('ALTER TABLE ship ADD tracking_number VARCHAR(255) DEFAULT NULL, ADD shipper VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP fixed_price, DROP weight_unit, DROP brand, DROP thickness, DROP thickness_unit, DROP width, DROP width_unit, DROP height, DROP height_unit, DROP length, DROP length_unit');
        $this->addSql('ALTER TABLE purchase DROP estimated_make_at, CHANGE status state INT NOT NULL');
        $this->addSql('ALTER TABLE ship DROP tracking_number, DROP shipper');
    }
}
