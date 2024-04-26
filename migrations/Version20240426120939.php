<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240426120939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album ADD artist_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('CREATE INDEX IDX_39986E43B7970CF8 ON album (artist_id)');
        $this->addSql('ALTER TABLE track ADD album_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A61137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('CREATE INDEX IDX_D6E3F8A61137ABCF ON track (album_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43B7970CF8');
        $this->addSql('DROP INDEX IDX_39986E43B7970CF8 ON album');
        $this->addSql('ALTER TABLE album DROP artist_id');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A61137ABCF');
        $this->addSql('DROP INDEX IDX_D6E3F8A61137ABCF ON track');
        $this->addSql('ALTER TABLE track DROP album_id');
    }
}
