<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230408090500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrator ADD priority_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD priority_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE carousel ADD priority_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE collaborator ADD priority_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partner ADD priority_order INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD priority_order INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrator DROP priority_order');
        $this->addSql('ALTER TABLE article DROP priority_order');
        $this->addSql('ALTER TABLE carousel DROP priority_order');
        $this->addSql('ALTER TABLE collaborator DROP priority_order');
        $this->addSql('ALTER TABLE partner DROP priority_order');
        $this->addSql('ALTER TABLE product DROP priority_order');
    }
}
