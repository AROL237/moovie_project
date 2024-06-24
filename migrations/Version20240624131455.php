<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240624131455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trailer ADD movie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trailer DROP movie');
        $this->addSql('ALTER TABLE trailer ADD CONSTRAINT FK_C691DC4E8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C691DC4E8F93B6FC ON trailer (movie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trailer DROP CONSTRAINT FK_C691DC4E8F93B6FC');
        $this->addSql('DROP INDEX UNIQ_C691DC4E8F93B6FC');
        $this->addSql('ALTER TABLE trailer ADD movie VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE trailer DROP movie_id');
    }
}
