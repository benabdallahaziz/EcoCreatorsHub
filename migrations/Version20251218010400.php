<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251218010400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, bio LONGTEXT NOT NULL, eco_technique VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, is_certified TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_1599687A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE certification_request (id INT AUTO_INCREMENT NOT NULL, motivation LONGTEXT NOT NULL, portfolio JSON NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, artist_id INT NOT NULL, INDEX IDX_6E7481A9B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, creation_journal_id INT DEFAULT NULL, tutorial_id INT DEFAULT NULL, INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526CC7053BE8 (creation_journal_id), INDEX IDX_9474526C89366B7B (tutorial_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE creation_journal (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date DATETIME NOT NULL, images JSON NOT NULL, is_published TINYINT(1) NOT NULL, category_id INT DEFAULT NULL, artist_id INT DEFAULT NULL, INDEX IDX_E1A6352612469DE2 (category_id), INDEX IDX_E1A63526B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE creation_step (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, step_order INT NOT NULL, images JSON DEFAULT NULL, creation_journal_id INT NOT NULL, INDEX IDX_9ECFB818C7053BE8 (creation_journal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE eco_tip (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, category VARCHAR(100) NOT NULL, is_approved TINYINT(1) NOT NULL, votes INT NOT NULL, image JSON DEFAULT NULL, created_at DATETIME NOT NULL, author_id INT NOT NULL, INDEX IDX_F54C5FFEF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE eco_tip_vote (id INT AUTO_INCREMENT NOT NULL, is_upvote TINYINT(1) NOT NULL, voted_at DATETIME NOT NULL, user_id INT NOT NULL, eco_tip_id INT NOT NULL, INDEX IDX_731465EBA76ED395 (user_id), INDEX IDX_731465EB9B52D73F (eco_tip_id), UNIQUE INDEX user_ecotip_unique (user_id, eco_tip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE journal_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_D6FCB478989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE journal_image (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(100) DEFAULT NULL, file_size INT DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, uploaded_at DATETIME NOT NULL, creation_journal_id INT DEFAULT NULL, creation_step_id INT DEFAULT NULL, INDEX IDX_26E63775C7053BE8 (creation_journal_id), INDEX IDX_26E6377553B3CCF7 (creation_step_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE journal_like (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, creation_journal_id INT NOT NULL, INDEX IDX_145B783BA76ED395 (user_id), INDEX IDX_145B783BC7053BE8 (creation_journal_id), UNIQUE INDEX UNIQ_145B783BA76ED395C7053BE8 (user_id, creation_journal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE journal_view (id INT AUTO_INCREMENT NOT NULL, viewed_at DATETIME NOT NULL, user_id INT NOT NULL, journal_id INT NOT NULL, INDEX IDX_46C59306A76ED395 (user_id), INDEX IDX_46C59306478E8802 (journal_id), UNIQUE INDEX UNIQ_46C59306A76ED395478E8802 (user_id, journal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, creation_journal_id INT DEFAULT NULL, tutorial_id INT DEFAULT NULL, INDEX IDX_AC6340B3A76ED395 (user_id), INDEX IDX_AC6340B3C7053BE8 (creation_journal_id), INDEX IDX_AC6340B389366B7B (tutorial_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, eco_score INT NOT NULL, is_eco_friendly TINYINT(1) NOT NULL, supplier_id INT DEFAULT NULL, INDEX IDX_7CBE75952ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material_supplier (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, contact_email VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_technique (id INT AUTO_INCREMENT NOT NULL, added_at DATETIME NOT NULL, project_id INT NOT NULL, technique_id INT NOT NULL, INDEX IDX_C607DD96166D1F9C (project_id), INDEX IDX_C607DD961F8ACB26 (technique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, follower_id INT NOT NULL, followed_id INT NOT NULL, INDEX IDX_A3C664D3AC24F853 (follower_id), INDEX IDX_A3C664D3D956F010 (followed_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE technique (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, category VARCHAR(100) NOT NULL, difficulty VARCHAR(50) NOT NULL, images JSON DEFAULT NULL, materials LONGTEXT DEFAULT NULL, steps LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE technique_eco_tip (technique_id INT NOT NULL, eco_tip_id INT NOT NULL, INDEX IDX_ED7C108C1F8ACB26 (technique_id), INDEX IDX_ED7C108C9B52D73F (eco_tip_id), PRIMARY KEY(technique_id, eco_tip_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tutorial (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, level VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, artist_id INT NOT NULL, INDEX IDX_C66BFFE9B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tutorial_material (tutorial_id INT NOT NULL, material_id INT NOT NULL, INDEX IDX_ACA0E41589366B7B (tutorial_id), INDEX IDX_ACA0E415E308AC6F (material_id), PRIMARY KEY(tutorial_id, material_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tutorial_resource (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, file_path VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, tutorial_id INT NOT NULL, INDEX IDX_6C8F659689366B7B (tutorial_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE artist ADD CONSTRAINT FK_1599687A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE certification_request ADD CONSTRAINT FK_6E7481A9B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CC7053BE8 FOREIGN KEY (creation_journal_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C89366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id)');
        $this->addSql('ALTER TABLE creation_journal ADD CONSTRAINT FK_E1A6352612469DE2 FOREIGN KEY (category_id) REFERENCES journal_category (id)');
        $this->addSql('ALTER TABLE creation_journal ADD CONSTRAINT FK_E1A63526B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE creation_step ADD CONSTRAINT FK_9ECFB818C7053BE8 FOREIGN KEY (creation_journal_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE eco_tip ADD CONSTRAINT FK_F54C5FFEF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE eco_tip_vote ADD CONSTRAINT FK_731465EBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE eco_tip_vote ADD CONSTRAINT FK_731465EB9B52D73F FOREIGN KEY (eco_tip_id) REFERENCES eco_tip (id)');
        $this->addSql('ALTER TABLE journal_image ADD CONSTRAINT FK_26E63775C7053BE8 FOREIGN KEY (creation_journal_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE journal_image ADD CONSTRAINT FK_26E6377553B3CCF7 FOREIGN KEY (creation_step_id) REFERENCES creation_step (id)');
        $this->addSql('ALTER TABLE journal_like ADD CONSTRAINT FK_145B783BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE journal_like ADD CONSTRAINT FK_145B783BC7053BE8 FOREIGN KEY (creation_journal_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE journal_view ADD CONSTRAINT FK_46C59306A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE journal_view ADD CONSTRAINT FK_46C59306478E8802 FOREIGN KEY (journal_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3C7053BE8 FOREIGN KEY (creation_journal_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B389366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id)');
        $this->addSql('ALTER TABLE material ADD CONSTRAINT FK_7CBE75952ADD6D8C FOREIGN KEY (supplier_id) REFERENCES material_supplier (id)');
        $this->addSql('ALTER TABLE project_technique ADD CONSTRAINT FK_C607DD96166D1F9C FOREIGN KEY (project_id) REFERENCES creation_journal (id)');
        $this->addSql('ALTER TABLE project_technique ADD CONSTRAINT FK_C607DD961F8ACB26 FOREIGN KEY (technique_id) REFERENCES technique (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3AC24F853 FOREIGN KEY (follower_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3D956F010 FOREIGN KEY (followed_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE technique_eco_tip ADD CONSTRAINT FK_ED7C108C1F8ACB26 FOREIGN KEY (technique_id) REFERENCES technique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE technique_eco_tip ADD CONSTRAINT FK_ED7C108C9B52D73F FOREIGN KEY (eco_tip_id) REFERENCES eco_tip (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutorial ADD CONSTRAINT FK_C66BFFE9B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE tutorial_material ADD CONSTRAINT FK_ACA0E41589366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutorial_material ADD CONSTRAINT FK_ACA0E415E308AC6F FOREIGN KEY (material_id) REFERENCES material (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tutorial_resource ADD CONSTRAINT FK_6C8F659689366B7B FOREIGN KEY (tutorial_id) REFERENCES tutorial (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist DROP FOREIGN KEY FK_1599687A76ED395');
        $this->addSql('ALTER TABLE certification_request DROP FOREIGN KEY FK_6E7481A9B7970CF8');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CC7053BE8');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C89366B7B');
        $this->addSql('ALTER TABLE creation_journal DROP FOREIGN KEY FK_E1A6352612469DE2');
        $this->addSql('ALTER TABLE creation_journal DROP FOREIGN KEY FK_E1A63526B7970CF8');
        $this->addSql('ALTER TABLE creation_step DROP FOREIGN KEY FK_9ECFB818C7053BE8');
        $this->addSql('ALTER TABLE eco_tip DROP FOREIGN KEY FK_F54C5FFEF675F31B');
        $this->addSql('ALTER TABLE eco_tip_vote DROP FOREIGN KEY FK_731465EBA76ED395');
        $this->addSql('ALTER TABLE eco_tip_vote DROP FOREIGN KEY FK_731465EB9B52D73F');
        $this->addSql('ALTER TABLE journal_image DROP FOREIGN KEY FK_26E63775C7053BE8');
        $this->addSql('ALTER TABLE journal_image DROP FOREIGN KEY FK_26E6377553B3CCF7');
        $this->addSql('ALTER TABLE journal_like DROP FOREIGN KEY FK_145B783BA76ED395');
        $this->addSql('ALTER TABLE journal_like DROP FOREIGN KEY FK_145B783BC7053BE8');
        $this->addSql('ALTER TABLE journal_view DROP FOREIGN KEY FK_46C59306A76ED395');
        $this->addSql('ALTER TABLE journal_view DROP FOREIGN KEY FK_46C59306478E8802');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3C7053BE8');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B389366B7B');
        $this->addSql('ALTER TABLE material DROP FOREIGN KEY FK_7CBE75952ADD6D8C');
        $this->addSql('ALTER TABLE project_technique DROP FOREIGN KEY FK_C607DD96166D1F9C');
        $this->addSql('ALTER TABLE project_technique DROP FOREIGN KEY FK_C607DD961F8ACB26');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3AC24F853');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3D956F010');
        $this->addSql('ALTER TABLE technique_eco_tip DROP FOREIGN KEY FK_ED7C108C1F8ACB26');
        $this->addSql('ALTER TABLE technique_eco_tip DROP FOREIGN KEY FK_ED7C108C9B52D73F');
        $this->addSql('ALTER TABLE tutorial DROP FOREIGN KEY FK_C66BFFE9B7970CF8');
        $this->addSql('ALTER TABLE tutorial_material DROP FOREIGN KEY FK_ACA0E41589366B7B');
        $this->addSql('ALTER TABLE tutorial_material DROP FOREIGN KEY FK_ACA0E415E308AC6F');
        $this->addSql('ALTER TABLE tutorial_resource DROP FOREIGN KEY FK_6C8F659689366B7B');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE certification_request');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE creation_journal');
        $this->addSql('DROP TABLE creation_step');
        $this->addSql('DROP TABLE eco_tip');
        $this->addSql('DROP TABLE eco_tip_vote');
        $this->addSql('DROP TABLE journal_category');
        $this->addSql('DROP TABLE journal_image');
        $this->addSql('DROP TABLE journal_like');
        $this->addSql('DROP TABLE journal_view');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE material_supplier');
        $this->addSql('DROP TABLE project_technique');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE technique');
        $this->addSql('DROP TABLE technique_eco_tip');
        $this->addSql('DROP TABLE tutorial');
        $this->addSql('DROP TABLE tutorial_material');
        $this->addSql('DROP TABLE tutorial_resource');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
