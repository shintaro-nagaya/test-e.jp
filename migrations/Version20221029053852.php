<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221029053852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(48) NOT NULL, admin_light_mode TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7D3656A4E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_news_category (id INT AUTO_INCREMENT NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(64) NOT NULL, enable TINYINT(1) NOT NULL, sort INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_news_child (id INT AUTO_INCREMENT NOT NULL, entry_id INT NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, sort INT NOT NULL, headline VARCHAR(128) DEFAULT NULL, image VARCHAR(128) DEFAULT NULL, image_width INT DEFAULT NULL, image_height INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, youtube_id VARCHAR(32) DEFAULT NULL, INDEX IDX_6BCCA8F2BA364942 (entry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cms_news_entry (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, entry_date DATE NOT NULL, title VARCHAR(255) NOT NULL, enable TINYINT(1) NOT NULL, content LONGTEXT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, publish_date DATETIME DEFAULT NULL, close_date DATETIME DEFAULT NULL, main_image VARCHAR(128) DEFAULT NULL, main_image_width INT DEFAULT NULL, main_image_height INT DEFAULT NULL, thumbnail VARCHAR(128) DEFAULT NULL, link_url VARCHAR(255) DEFAULT NULL, link_new_tab TINYINT(1) NOT NULL, INDEX IDX_625E61AB12469DE2 (category_id), INDEX IDX_625E61ABEBC4F69 (entry_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inquiry_contact_data (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) DEFAULT NULL, message LONGTEXT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, email VARCHAR(255) NOT NULL, ip VARCHAR(32) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cms_news_child ADD CONSTRAINT FK_6BCCA8F2BA364942 FOREIGN KEY (entry_id) REFERENCES cms_news_entry (id)');
        $this->addSql('ALTER TABLE cms_news_entry ADD CONSTRAINT FK_625E61AB12469DE2 FOREIGN KEY (category_id) REFERENCES cms_news_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cms_news_child DROP FOREIGN KEY FK_6BCCA8F2BA364942');
        $this->addSql('ALTER TABLE cms_news_entry DROP FOREIGN KEY FK_625E61AB12469DE2');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE cms_news_category');
        $this->addSql('DROP TABLE cms_news_child');
        $this->addSql('DROP TABLE cms_news_entry');
        $this->addSql('DROP TABLE inquiry_contact_data');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
