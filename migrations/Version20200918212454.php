<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200918212454 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invite_classroom DROP FOREIGN KEY FK_DDA3C4D8EA417747');
        $this->addSql('DROP TABLE invite');
        $this->addSql('DROP TABLE invite_classroom');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invite (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE invite_classroom (invite_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_DDA3C4D8EA417747 (invite_id), INDEX IDX_DDA3C4D86278D5A8 (classroom_id), PRIMARY KEY(invite_id, classroom_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE invite_classroom ADD CONSTRAINT FK_DDA3C4D86278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invite_classroom ADD CONSTRAINT FK_DDA3C4D8EA417747 FOREIGN KEY (invite_id) REFERENCES invite (id) ON DELETE CASCADE');
    }
}
