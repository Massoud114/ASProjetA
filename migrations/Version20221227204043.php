<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221227204043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, private TINYINT(1) DEFAULT 0 NOT NULL, promo_code VARCHAR(255) DEFAULT NULL, percentage INT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion_product (promotion_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_8B37F297139DF194 (promotion_id), INDEX IDX_8B37F2974584665A (product_id), PRIMARY KEY(promotion_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promotion_product ADD CONSTRAINT FK_8B37F297139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE promotion_product ADD CONSTRAINT FK_8B37F2974584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchase ADD promotion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id)');
        $this->addSql('CREATE INDEX IDX_6117D13B139DF194 ON purchase (promotion_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B139DF194');
        $this->addSql('ALTER TABLE promotion_product DROP FOREIGN KEY FK_8B37F297139DF194');
        $this->addSql('ALTER TABLE promotion_product DROP FOREIGN KEY FK_8B37F2974584665A');
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP TABLE promotion_product');
        $this->addSql('DROP INDEX IDX_6117D13B139DF194 ON purchase');
        $this->addSql('ALTER TABLE purchase DROP promotion_id');
    }
}
