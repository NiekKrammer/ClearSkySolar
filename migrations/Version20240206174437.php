<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206174437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398ED766068 FOREIGN KEY (username_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F5299398ED766068 ON `order` (username_id)');
        $this->addSql('ALTER TABLE order_items CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE order_items ADD CONSTRAINT FK_62809DB08D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_62809DB08D9F6D38 ON order_items (order_id)');
        $this->addSql('ALTER TABLE products ADD description LONGTEXT NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398ED766068');
        $this->addSql('DROP INDEX IDX_F5299398ED766068 ON `order`');
        $this->addSql('DROP INDEX `primary` ON `order`');
        $this->addSql('ALTER TABLE `order` CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE order_items MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE order_items DROP FOREIGN KEY FK_62809DB08D9F6D38');
        $this->addSql('DROP INDEX IDX_62809DB08D9F6D38 ON order_items');
        $this->addSql('DROP INDEX `primary` ON order_items');
        $this->addSql('ALTER TABLE order_items CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE products MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON products');
        $this->addSql('ALTER TABLE products DROP description, CHANGE id id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) DEFAULT NULL');
    }
}
