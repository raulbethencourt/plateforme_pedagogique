<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200921202346 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD41807E1D');
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD6278D5A8');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD41807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
        $this->addSql('ALTER TABLE classroom_student DROP FOREIGN KEY FK_3DD26E1B6278D5A8');
        $this->addSql('ALTER TABLE classroom_student DROP FOREIGN KEY FK_3DD26E1BCB944F1A');
        $this->addSql('ALTER TABLE classroom_student ADD CONSTRAINT FK_3DD26E1B6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
        $this->addSql('ALTER TABLE classroom_student ADD CONSTRAINT FK_3DD26E1BCB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classroom_student DROP FOREIGN KEY FK_3DD26E1B6278D5A8');
        $this->addSql('ALTER TABLE classroom_student DROP FOREIGN KEY FK_3DD26E1BCB944F1A');
        $this->addSql('ALTER TABLE classroom_student ADD CONSTRAINT FK_3DD26E1B6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_student ADD CONSTRAINT FK_3DD26E1BCB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD6278D5A8');
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD41807E1D');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD41807E1D FOREIGN KEY (teacher_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
