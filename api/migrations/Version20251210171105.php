<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210171105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usuario_trivia ADD estado SMALLINT DEFAULT 0 NOT NULL, ADD codigo VARCHAR(6) NOT NULL, ADD started_at DATETIME DEFAULT NULL, ADD finished_at DATETIME DEFAULT NULL, DROP completada');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C0A9939920332D99 ON usuario_trivia (codigo)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C0A9939920332D99 ON usuario_trivia');
        $this->addSql('ALTER TABLE usuario_trivia ADD completada TINYINT NOT NULL, DROP estado, DROP codigo, DROP started_at, DROP finished_at');
    }
}
