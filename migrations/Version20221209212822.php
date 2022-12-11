<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221209212822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE social_media (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255), icon VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO social_media (id, name, url, icon) VALUES(null, 'facebook', null, 'facebook'), (null, 'instagram', null, 'instagram'), (null, 'twitter', null, 'twitter'), (null, 'tiktok', null, 'tiktok'), (null, 'snapchat', null, 'snapchat'), (null, 'discord', null, 'discord'), (null, 'google', null, 'google')");
		$this->addSql('ALTER TABLE category ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE social_media');
        $this->addSql('ALTER TABLE category DROP created_at');
    }
}
