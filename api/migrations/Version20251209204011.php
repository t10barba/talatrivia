<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209204011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pregunta (id INT AUTO_INCREMENT NOT NULL, dificultad SMALLINT NOT NULL, texto LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE pregunta_trivia (orden INT NOT NULL, created_at DATETIME NOT NULL, trivia_id INT NOT NULL, pregunta_id INT NOT NULL, INDEX IDX_3A29F28AE8DE6E08 (trivia_id), INDEX IDX_3A29F28A31A5801E (pregunta_id), PRIMARY KEY (trivia_id, pregunta_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE respuesta (id INT AUTO_INCREMENT NOT NULL, texto LONGTEXT NOT NULL, es_correcta TINYINT NOT NULL, created_at DATETIME NOT NULL, pregunta_id INT NOT NULL, INDEX IDX_6C6EC5EE31A5801E (pregunta_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE respuesta_usuario (id INT AUTO_INCREMENT NOT NULL, es_correcta TINYINT NOT NULL, puntaje_obtenido INT NOT NULL, created_at DATETIME NOT NULL, usuario_trivia_id INT NOT NULL, pregunta_id INT NOT NULL, respuesta_id INT NOT NULL, INDEX IDX_306E223961E5841A (usuario_trivia_id), INDEX IDX_306E223931A5801E (pregunta_id), INDEX IDX_306E2239D9BA57A2 (respuesta_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE trivia (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2265B05DE7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE usuario_trivia (id INT AUTO_INCREMENT NOT NULL, puntaje_total INT DEFAULT NULL, completada TINYINT NOT NULL, created_at DATETIME NOT NULL, trivia_id INT NOT NULL, usuario_id INT NOT NULL, INDEX IDX_C0A99399E8DE6E08 (trivia_id), INDEX IDX_C0A99399DB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE pregunta_trivia ADD CONSTRAINT FK_3A29F28AE8DE6E08 FOREIGN KEY (trivia_id) REFERENCES trivia (id)');
        $this->addSql('ALTER TABLE pregunta_trivia ADD CONSTRAINT FK_3A29F28A31A5801E FOREIGN KEY (pregunta_id) REFERENCES pregunta (id)');
        $this->addSql('ALTER TABLE respuesta ADD CONSTRAINT FK_6C6EC5EE31A5801E FOREIGN KEY (pregunta_id) REFERENCES pregunta (id)');
        $this->addSql('ALTER TABLE respuesta_usuario ADD CONSTRAINT FK_306E223961E5841A FOREIGN KEY (usuario_trivia_id) REFERENCES usuario_trivia (id)');
        $this->addSql('ALTER TABLE respuesta_usuario ADD CONSTRAINT FK_306E223931A5801E FOREIGN KEY (pregunta_id) REFERENCES pregunta (id)');
        $this->addSql('ALTER TABLE respuesta_usuario ADD CONSTRAINT FK_306E2239D9BA57A2 FOREIGN KEY (respuesta_id) REFERENCES respuesta (id)');
        $this->addSql('ALTER TABLE usuario_trivia ADD CONSTRAINT FK_C0A99399E8DE6E08 FOREIGN KEY (trivia_id) REFERENCES trivia (id)');
        $this->addSql('ALTER TABLE usuario_trivia ADD CONSTRAINT FK_C0A99399DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pregunta_trivia DROP FOREIGN KEY FK_3A29F28AE8DE6E08');
        $this->addSql('ALTER TABLE pregunta_trivia DROP FOREIGN KEY FK_3A29F28A31A5801E');
        $this->addSql('ALTER TABLE respuesta DROP FOREIGN KEY FK_6C6EC5EE31A5801E');
        $this->addSql('ALTER TABLE respuesta_usuario DROP FOREIGN KEY FK_306E223961E5841A');
        $this->addSql('ALTER TABLE respuesta_usuario DROP FOREIGN KEY FK_306E223931A5801E');
        $this->addSql('ALTER TABLE respuesta_usuario DROP FOREIGN KEY FK_306E2239D9BA57A2');
        $this->addSql('ALTER TABLE usuario_trivia DROP FOREIGN KEY FK_C0A99399E8DE6E08');
        $this->addSql('ALTER TABLE usuario_trivia DROP FOREIGN KEY FK_C0A99399DB38439E');
        $this->addSql('DROP TABLE pregunta');
        $this->addSql('DROP TABLE pregunta_trivia');
        $this->addSql('DROP TABLE respuesta');
        $this->addSql('DROP TABLE respuesta_usuario');
        $this->addSql('DROP TABLE trivia');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE usuario_trivia');
    }
}
