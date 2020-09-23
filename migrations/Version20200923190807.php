<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200923190807 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classroom (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, access_code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom_teacher (classroom_id INT NOT NULL, teacher_id INT NOT NULL, INDEX IDX_3A0767FD6278D5A8 (classroom_id), INDEX IDX_3A0767FD41807E1D (teacher_id), PRIMARY KEY(classroom_id, teacher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom_student (classroom_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_3DD26E1B6278D5A8 (classroom_id), INDEX IDX_3DD26E1BCB944F1A (student_id), PRIMARY KEY(classroom_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pass (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, questionnaire_id INT NOT NULL, points INT NOT NULL, date_realisation DATE NOT NULL, INDEX IDX_CE70D424CB944F1A (student_id), INDEX IDX_CE70D424CE07E8FF (questionnaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposition (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, text VARCHAR(255) NOT NULL, correct TINYINT(1) DEFAULT NULL, INDEX IDX_C7CDC3531E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, questionnaire_id INT NOT NULL, wording VARCHAR(255) NOT NULL, score NUMERIC(5, 2) NOT NULL, INDEX IDX_B6F7494ECE07E8FF (questionnaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questionnaire (id INT AUTO_INCREMENT NOT NULL, teacher_id INT NOT NULL, title VARCHAR(255) NOT NULL, difficulty VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, INDEX IDX_7A64DAF41807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, surname VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, photo_name VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, entry_date DATE NOT NULL, type VARCHAR(255) NOT NULL, hobby VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_teacher ADD CONSTRAINT FK_3A0767FD41807E1D FOREIGN KEY (teacher_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_student ADD CONSTRAINT FK_3DD26E1B6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classroom_student ADD CONSTRAINT FK_3DD26E1BCB944F1A FOREIGN KEY (student_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pass ADD CONSTRAINT FK_CE70D424CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pass ADD CONSTRAINT FK_CE70D424CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC3531E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494ECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE questionnaire ADD CONSTRAINT FK_7A64DAF41807E1D FOREIGN KEY (teacher_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD6278D5A8');
        $this->addSql('ALTER TABLE classroom_student DROP FOREIGN KEY FK_3DD26E1B6278D5A8');
        $this->addSql('ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC3531E27F6BF');
        $this->addSql('ALTER TABLE pass DROP FOREIGN KEY FK_CE70D424CE07E8FF');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494ECE07E8FF');
        $this->addSql('ALTER TABLE classroom_teacher DROP FOREIGN KEY FK_3A0767FD41807E1D');
        $this->addSql('ALTER TABLE classroom_student DROP FOREIGN KEY FK_3DD26E1BCB944F1A');
        $this->addSql('ALTER TABLE pass DROP FOREIGN KEY FK_CE70D424CB944F1A');
        $this->addSql('ALTER TABLE questionnaire DROP FOREIGN KEY FK_7A64DAF41807E1D');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('DROP TABLE classroom_teacher');
        $this->addSql('DROP TABLE classroom_student');
        $this->addSql('DROP TABLE pass');
        $this->addSql('DROP TABLE proposition');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE questionnaire');
        $this->addSql('DROP TABLE user');
    }
}
