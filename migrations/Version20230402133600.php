<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230402133600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66A76ED395');
        $this->addSql('DROP INDEX IDX_23A0E66A76ED395 ON article');
        $this->addSql('ALTER TABLE article DROP user_id, CHANGE front_page is_front_page TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE collaborator DROP FOREIGN KEY FK_606D487CA76ED395');
        $this->addSql('DROP INDEX IDX_606D487CA76ED395 ON collaborator');
        $this->addSql('ALTER TABLE collaborator DROP user_id');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16A76ED395');
        $this->addSql('DROP INDEX IDX_312B3E16A76ED395 ON partner');
        $this->addSql('ALTER TABLE partner DROP user_id');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA76ED395');
        $this->addSql('DROP INDEX IDX_D34A04ADA76ED395 ON product');
        $this->addSql('ALTER TABLE product DROP user_id');
        $this->addSql('ALTER TABLE user DROP organization_name, DROP site_title, DROP address, DROP postal, DROP country, DROP phone, DROP facebook, DROP instagram, DROP twitter, DROP short_description, DROP description, DROP logo, DROP administrator_firstname, DROP administrator_lastname, DROP city');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD user_id INT NOT NULL, CHANGE is_front_page front_page TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
        $this->addSql('ALTER TABLE collaborator ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE collaborator ADD CONSTRAINT FK_606D487CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_606D487CA76ED395 ON collaborator (user_id)');
        $this->addSql('ALTER TABLE partner ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_312B3E16A76ED395 ON partner (user_id)');
        $this->addSql('ALTER TABLE product ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADA76ED395 ON product (user_id)');
        $this->addSql('ALTER TABLE user ADD organization_name VARCHAR(255) NOT NULL, ADD site_title VARCHAR(255) NOT NULL, ADD address VARCHAR(255) NOT NULL, ADD postal VARCHAR(255) NOT NULL, ADD country VARCHAR(255) NOT NULL, ADD phone VARCHAR(255) NOT NULL, ADD facebook VARCHAR(255) DEFAULT NULL, ADD instagram VARCHAR(255) DEFAULT NULL, ADD twitter VARCHAR(255) DEFAULT NULL, ADD short_description LONGTEXT NOT NULL, ADD description LONGTEXT NOT NULL, ADD logo VARCHAR(255) DEFAULT NULL, ADD administrator_firstname VARCHAR(255) NOT NULL, ADD administrator_lastname VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL');
    }
}
