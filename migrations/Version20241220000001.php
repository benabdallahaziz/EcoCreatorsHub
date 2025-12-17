<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add journal categories and publication status';
    }

    public function up(Schema $schema): void
    {
        // Create journal_category table
        $this->addSql('CREATE TABLE journal_category (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            description LONGTEXT DEFAULT NULL, 
            slug VARCHAR(100) NOT NULL, 
            UNIQUE INDEX UNIQ_journal_category_slug (slug), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create journal_image table
        $this->addSql('CREATE TABLE journal_image (
            id INT AUTO_INCREMENT NOT NULL, 
            creation_journal_id INT DEFAULT NULL, 
            creation_step_id INT DEFAULT NULL, 
            filename VARCHAR(255) NOT NULL, 
            original_name VARCHAR(255) DEFAULT NULL, 
            mime_type VARCHAR(100) DEFAULT NULL, 
            file_size INT DEFAULT NULL, 
            alt VARCHAR(255) DEFAULT NULL, 
            uploaded_at DATETIME NOT NULL, 
            INDEX IDX_journal_image_creation_journal (creation_journal_id), 
            INDEX IDX_journal_image_creation_step (creation_step_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add is_published and category_id columns to creation_journal
        $this->addSql('ALTER TABLE creation_journal ADD is_published TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE creation_journal ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE creation_journal ADD INDEX IDX_creation_journal_category (category_id)');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE creation_journal ADD CONSTRAINT FK_creation_journal_category 
            FOREIGN KEY (category_id) REFERENCES journal_category (id)');
        $this->addSql('ALTER TABLE journal_image ADD CONSTRAINT FK_journal_image_creation_journal 
            FOREIGN KEY (creation_journal_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE journal_image ADD CONSTRAINT FK_journal_image_creation_step 
            FOREIGN KEY (creation_step_id) REFERENCES creation_step (id)');
    }

    public function down(Schema $schema): void
    {
        // Remove foreign key constraints
        $this->addSql('ALTER TABLE creation_journal DROP FOREIGN KEY FK_creation_journal_category');
        $this->addSql('ALTER TABLE journal_image DROP FOREIGN KEY FK_journal_image_creation_journal');
        $this->addSql('ALTER TABLE journal_image DROP FOREIGN KEY FK_journal_image_creation_step');

        // Remove columns from creation_journal
        $this->addSql('ALTER TABLE creation_journal DROP INDEX IDX_creation_journal_category');
        $this->addSql('ALTER TABLE creation_journal DROP category_id');
        $this->addSql('ALTER TABLE creation_journal DROP is_published');

        // Drop tables
        $this->addSql('DROP TABLE journal_image');
        $this->addSql('DROP TABLE journal_category');
    }
}