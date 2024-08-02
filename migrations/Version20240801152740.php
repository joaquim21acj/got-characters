<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240801152740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE actor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE character_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE house_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE actor (id INT NOT NULL, character_id INT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_447556F91136BE75 ON actor (character_id)');
        $this->addSql('CREATE TABLE character (id INT NOT NULL, house_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) DEFAULT NULL, image_thumb VARCHAR(255) DEFAULT NULL, image_full VARCHAR(255) DEFAULT NULL, royal BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_937AB0346BB74515 ON character (house_id)');
        $this->addSql('CREATE TABLE house (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE actor ADD CONSTRAINT FK_447556F91136BE75 FOREIGN KEY (character_id) REFERENCES character (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE character ADD CONSTRAINT FK_937AB0346BB74515 FOREIGN KEY (house_id) REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE actor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE character_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE house_id_seq CASCADE');
        $this->addSql('ALTER TABLE actor DROP CONSTRAINT FK_447556F91136BE75');
        $this->addSql('ALTER TABLE character DROP CONSTRAINT FK_937AB0346BB74515');
        $this->addSql('DROP TABLE actor');
        $this->addSql('DROP TABLE character');
        $this->addSql('DROP TABLE house');
    }
}
