<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200911192317 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, formateur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_8F87BF96155D8F51 (formateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, classe_id INT NOT NULL, username VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, code_etudiant VARCHAR(255) DEFAULT NULL, photo_name VARCHAR(255) DEFAULT NULL, code_formateur VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D6498F5EA509 (classe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questionnaire (id INT AUTO_INCREMENT NOT NULL, formateur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, difficulte VARCHAR(255) NOT NULL, INDEX IDX_7A64DAF155D8F51 (formateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, questionnaire_id INT NOT NULL, enonce VARCHAR(255) NOT NULL, score NUMERIC(5, 2) NOT NULL, INDEX IDX_B6F7494ECE07E8FF (questionnaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proposition (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, texte VARCHAR(255) NOT NULL, correct TINYINT(1) DEFAULT NULL, INDEX IDX_C7CDC3531E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE passer (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT NOT NULL, questionnaire_id INT NOT NULL, points INT NOT NULL, date_realisation DATE NOT NULL, INDEX IDX_970EA416DDEAB1A3 (etudiant_id), INDEX IDX_970EA416CE07E8FF (questionnaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF96155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE questionnaire ADD CONSTRAINT FK_7A64DAF155D8F51 FOREIGN KEY (formateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494ECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE proposition ADD CONSTRAINT FK_C7CDC3531E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE passer ADD CONSTRAINT FK_970EA416DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE passer ADD CONSTRAINT FK_970EA416CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498F5EA509');
        $this->addSql('ALTER TABLE proposition DROP FOREIGN KEY FK_C7CDC3531E27F6BF');
        $this->addSql('ALTER TABLE passer DROP FOREIGN KEY FK_970EA416CE07E8FF');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494ECE07E8FF');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF96155D8F51');
        $this->addSql('ALTER TABLE passer DROP FOREIGN KEY FK_970EA416DDEAB1A3');
        $this->addSql('ALTER TABLE questionnaire DROP FOREIGN KEY FK_7A64DAF155D8F51');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE passer');
        $this->addSql('DROP TABLE proposition');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE questionnaire');
        $this->addSql('DROP TABLE user');
    }
}
