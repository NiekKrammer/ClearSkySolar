<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240305134353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398ED766068');
        $this->addSql('DROP INDEX IDX_F5299398ED766068 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD created_at DATETIME NOT NULL, DROP username_id, DROP name, DROP phone_nr, DROP date, DROP time, DROP ordered_at, CHANGE address password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP username');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD username_id INT DEFAULT NULL, ADD name VARCHAR(100) NOT NULL, ADD phone_nr VARCHAR(100) NOT NULL, ADD time TIME DEFAULT NULL, ADD ordered_at DATETIME NOT NULL, DROP roles, CHANGE password address VARCHAR(255) NOT NULL, CHANGE created_at date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398ED766068 FOREIGN KEY (username_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F5299398ED766068 ON `order` (username_id)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(180) NOT NULL');
    }
}
