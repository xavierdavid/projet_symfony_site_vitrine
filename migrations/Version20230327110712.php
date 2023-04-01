<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327110712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_document (article_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_8D817E1D7294869C (article_id), INDEX IDX_8D817E1DC33F7837 (document_id), PRIMARY KEY(article_id, document_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, document_file VARCHAR(255) NOT NULL, caption VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_document (product_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_13D9E0E14584665A (product_id), INDEX IDX_13D9E0E1C33F7837 (document_id), PRIMARY KEY(product_id, document_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article_document ADD CONSTRAINT FK_8D817E1D7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_document ADD CONSTRAINT FK_8D817E1DC33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_document ADD CONSTRAINT FK_13D9E0E14584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_document ADD CONSTRAINT FK_13D9E0E1C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_file DROP FOREIGN KEY FK_3CDDB1117294869C');
        $this->addSql('ALTER TABLE article_file DROP FOREIGN KEY FK_3CDDB11193CB796C');
        $this->addSql('ALTER TABLE product_file DROP FOREIGN KEY FK_17714B14584665A');
        $this->addSql('ALTER TABLE product_file DROP FOREIGN KEY FK_17714B193CB796C');
        $this->addSql('DROP TABLE article_file');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE product_file');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_file (article_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_3CDDB1117294869C (article_id), INDEX IDX_3CDDB11193CB796C (file_id), PRIMARY KEY(article_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, document_file VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, caption VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0E3BD61CE (available_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_file (product_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_17714B193CB796C (file_id), INDEX IDX_17714B14584665A (product_id), PRIMARY KEY(product_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE article_file ADD CONSTRAINT FK_3CDDB1117294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_file ADD CONSTRAINT FK_3CDDB11193CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_file ADD CONSTRAINT FK_17714B14584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_file ADD CONSTRAINT FK_17714B193CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article_document DROP FOREIGN KEY FK_8D817E1D7294869C');
        $this->addSql('ALTER TABLE article_document DROP FOREIGN KEY FK_8D817E1DC33F7837');
        $this->addSql('ALTER TABLE product_document DROP FOREIGN KEY FK_13D9E0E14584665A');
        $this->addSql('ALTER TABLE product_document DROP FOREIGN KEY FK_13D9E0E1C33F7837');
        $this->addSql('DROP TABLE article_document');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE product_document');
    }
}
