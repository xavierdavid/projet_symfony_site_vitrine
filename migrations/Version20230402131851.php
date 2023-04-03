<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402131851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, organization_name VARCHAR(255) NOT NULL, site_title VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, facebook VARCHAR(255) DEFAULT NULL, instagram VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, short_description LONGTEXT NOT NULL, description LONGTEXT NOT NULL, logo VARCHAR(255) DEFAULT NULL, administrator_firstname VARCHAR(255) NOT NULL, administrator_lastname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE organization');
    }
}
