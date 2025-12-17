<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251217142500 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE journal_view (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, journal_id INT NOT NULL, viewed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A76ED395A76ED395 (user_id), INDEX IDX_A76ED395478E8802 (journal_id), UNIQUE INDEX UNIQ_A76ED395A76ED395478E8802 (user_id, journal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE journal_view ADD CONSTRAINT FK_A76ED395A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE journal_view ADD CONSTRAINT FK_A76ED395478E8802 FOREIGN KEY (journal_id) REFERENCES creation_journal (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE journal_view DROP FOREIGN KEY FK_A76ED395A76ED395');
        $this->addSql('ALTER TABLE journal_view DROP FOREIGN KEY FK_A76ED395478E8802');
        $this->addSql('DROP TABLE journal_view');
    }
}