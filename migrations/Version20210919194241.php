<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210919194241 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avatar (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, image_name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1677722FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, discipline VARCHAR(255) DEFAULT NULL, location VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE events (id_events INT AUTO_INCREMENT NOT NULL, begin_at DATETIME NOT NULL, end_at DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id_events)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, level VARCHAR(255) DEFAULT NULL, date_creation DATE NOT NULL, creator VARCHAR(255) NOT NULL, visibility TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_questionnaire (lesson_id INT NOT NULL, questionnaire_id INT NOT NULL, INDEX IDX_D38A5E4DCDF80196 (lesson_id), INDEX IDX_D38A5E4DCE07E8FF (questionnaire_id), PRIMARY KEY(lesson_id, questionnaire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_user (lesson_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B4E2102DCDF80196 (lesson_id), INDEX IDX_B4E2102DA76ED395 (user_id), PRIMARY KEY(lesson_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_classroom (lesson_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_2D5EDF17CDF80196 (lesson_id), INDEX IDX_2D5EDF176278D5A8 (classroom_id), PRIMARY KEY(lesson_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, creator VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link_classroom (link_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_AB08E0E9ADA40271 (link_id), INDEX IDX_AB08E0E96278D5A8 (classroom_id), PRIMARY KEY(link_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, classroom_id INT NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_BF5476CA6278D5A8 (classroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pass (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, questionnaire_id INT NOT NULL, points INT NOT NULL, date_realisation DATE NOT NULL, INDEX IDX_CE70D424CB944F1A (student_id), INDEX IDX_CE70D424CE07E8FF (questionnaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposition (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, text VARCHAR(255) NOT NULL, correct TINYINT(1) DEFAULT NULL, INDEX IDX_C7CDC3531E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, questionnaire_id INT NOT NULL, title VARCHAR(255) NOT NULL, score INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B6F7494ECE07E8FF (questionnaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questionnaire (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, difficulty VARCHAR(255) NOT NULL, date_creation DATE NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, creator VARCHAR(255) NOT NULL, visibility TINYINT(1) DEFAULT NULL, realisation_time INT NOT NULL, level VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, link_description VARCHAR(255) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, instructions LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, surname VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, entry_date DATE NOT NULL, telephone VARCHAR(10) DEFAULT NULL, type VARCHAR(255) NOT NULL, hobby VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_classroom (user_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_499DBD79A76ED395 (user_id), INDEX IDX_499DBD796278D5A8 (classroom_id), PRIMARY KEY(user_id, classroom_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avatar ADD CONSTRAINT FK_1677722FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson_questionnaire ADD CONSTRAINT FK_D38A5E4DCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_questionnaire ADD CONSTRAINT FK_D38A5E4DCE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_user ADD CONSTRAINT FK_B4E2102DCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_user ADD CONSTRAINT FK_B4E2102DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_classroom ADD CONSTRAINT FK_2D5EDF17CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_classroom ADD CONSTRAINT FK_2D5EDF176278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_classroom ADD CONSTRAINT FK_AB08E0E9ADA40271 FOREIGN KEY (link_id) REFERENCES link (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE link_classroom ADD CONSTRAINT FK_AB08E0E96278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
        $this->addSql('ALTER TABLE pass ADD CONSTRAINT FK_CE70D424CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pass ADD CONSTRAINT FK_CE70D424CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC3531E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494ECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE user_classroom ADD CONSTRAINT FK_499DBD79A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_classroom ADD CONSTRAINT FK_499DBD796278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson_classroom DROP FOREIGN KEY FK_2D5EDF176278D5A8');
        $this->addSql('ALTER TABLE link_classroom DROP FOREIGN KEY FK_AB08E0E96278D5A8');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA6278D5A8');
        $this->addSql('ALTER TABLE user_classroom DROP FOREIGN KEY FK_499DBD796278D5A8');
        $this->addSql('ALTER TABLE lesson_questionnaire DROP FOREIGN KEY FK_D38A5E4DCDF80196');
        $this->addSql('ALTER TABLE lesson_user DROP FOREIGN KEY FK_B4E2102DCDF80196');
        $this->addSql('ALTER TABLE lesson_classroom DROP FOREIGN KEY FK_2D5EDF17CDF80196');
        $this->addSql('ALTER TABLE link_classroom DROP FOREIGN KEY FK_AB08E0E9ADA40271');
        $this->addSql('ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC3531E27F6BF');
        $this->addSql('ALTER TABLE lesson_questionnaire DROP FOREIGN KEY FK_D38A5E4DCE07E8FF');
        $this->addSql('ALTER TABLE pass DROP FOREIGN KEY FK_CE70D424CE07E8FF');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494ECE07E8FF');
        $this->addSql('ALTER TABLE avatar DROP FOREIGN KEY FK_1677722FA76ED395');
        $this->addSql('ALTER TABLE lesson_user DROP FOREIGN KEY FK_B4E2102DA76ED395');
        $this->addSql('ALTER TABLE pass DROP FOREIGN KEY FK_CE70D424CB944F1A');
        $this->addSql('ALTER TABLE user_classroom DROP FOREIGN KEY FK_499DBD79A76ED395');
        $this->addSql('DROP TABLE avatar');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_questionnaire');
        $this->addSql('DROP TABLE lesson_user');
        $this->addSql('DROP TABLE lesson_classroom');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE link_classroom');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE pass');
        $this->addSql('DROP TABLE proposition');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE questionnaire');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_classroom');
    }
}
