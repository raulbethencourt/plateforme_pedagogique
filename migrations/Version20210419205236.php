<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419205236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson ADD visibility TINYINT(1) NOT NULL, ADD playable TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE questionnaire ADD visibility TINYINT(1) NOT NULL, ADD playable TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson DROP visibility, DROP playable');
        $this->addSql('ALTER TABLE questionnaire DROP visibility, DROP playable');
    }
}
