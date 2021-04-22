<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210422172131 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, creator VARCHAR(255) NOT NULL, visibility TINYINT(1) NOT NULL, usable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_classroom (link_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_AB08E0E9ADA40271 (link_id), INDEX IDX_AB08E0E96278D5A8 (classroom_id), PRIMARY KEY(link_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE link_classroom ADD CONSTRAINT FK_AB08E0E9ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_classroom ADD CONSTRAINT FK_AB08E0E96278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE link_classroom DROP FOREIGN KEY FK_AB08E0E9ADA40271');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE link_classroom');
    }
}
