<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221227071815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE finance_flow (id INT AUTO_INCREMENT NOT NULL, amount DOUBLE PRECISION NOT NULL, type VARCHAR(2) NOT NULL, denomination VARCHAR(255) NOT NULL, description TINYTEXT DEFAULT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product CHANGE min_price min_price INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase DROP confirmed');
        $this->addSql('ALTER TABLE user CHANGE pronoun pronoun VARCHAR(4) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE finance_flow');
        $this->addSql('ALTER TABLE product CHANGE min_price min_price INT NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD confirmed TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE pronoun pronoun LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
    }
}
