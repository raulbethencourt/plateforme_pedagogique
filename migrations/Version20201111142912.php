<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201111142912 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE events DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE events CHANGE id id_events INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE events ADD PRIMARY KEY (id_events)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE events MODIFY id_events INT NOT NULL');
        $this->addSql('ALTER TABLE events DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE events CHANGE id_events id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE events ADD PRIMARY KEY (id)');
    }
}
