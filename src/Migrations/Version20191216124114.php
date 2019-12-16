<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191216124114 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE matrice DROP FOREIGN KEY FK_4DCF5DA0E70AF195');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA4B501A03');
        $this->addSql('CREATE TABLE indicateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE dictionnaire');
        $this->addSql('DROP TABLE matrice');
        $this->addSql('DROP TABLE similar_reports');
        $this->addSql('DROP TABLE structure');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dictionnaire (id INT AUTO_INCREMENT NOT NULL, synonyme VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, definition LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE matrice (id INT AUTO_INCREMENT NOT NULL, nom_rapport_id INT DEFAULT NULL, dictionnaire_id INT DEFAULT NULL, axe_analyse VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, indicateur VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_4DCF5DA0E70AF195 (dictionnaire_id), INDEX IDX_4DCF5DA01CB44C31 (nom_rapport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE similar_reports (id INT AUTO_INCREMENT NOT NULL, current_report VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, similar_report VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE structure (id INT AUTO_INCREMENT NOT NULL, nom_hierarchie_id INT DEFAULT NULL, structure JSON NOT NULL, INDEX IDX_6F0137EA4B501A03 (nom_hierarchie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE matrice ADD CONSTRAINT FK_4DCF5DA01CB44C31 FOREIGN KEY (nom_rapport_id) REFERENCES report_catalog (id)');
        $this->addSql('ALTER TABLE matrice ADD CONSTRAINT FK_4DCF5DA0E70AF195 FOREIGN KEY (dictionnaire_id) REFERENCES dictionnaire (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA4B501A03 FOREIGN KEY (nom_hierarchie_id) REFERENCES matrice (id)');
        $this->addSql('DROP TABLE indicateur');
    }
}
