<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320083816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD is_rgpd TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE file ADD document_file VARCHAR(255) NOT NULL, ADD caption VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE image ADD image_file VARCHAR(255) NOT NULL, ADD caption VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP is_rgpd');
        $this->addSql('ALTER TABLE file DROP document_file, DROP caption');
        $this->addSql('ALTER TABLE image DROP image_file, DROP caption');
        $this->addSql('ALTER TABLE user DROP reset_token');
    }
}
