<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191120063027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dossier (id INT AUTO_INCREMENT NOT NULL, nom_dossier VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_catalog (id INT AUTO_INCREMENT NOT NULL, sub_folder_id INT DEFAULT NULL, main_folder_id INT DEFAULT NULL, user_id INT DEFAULT NULL, n INT NOT NULL, nom_rapport VARCHAR(255) NOT NULL, version_actuelle VARCHAR(255) NOT NULL, commentaire LONGTEXT NOT NULL, categorie VARCHAR(255) NOT NULL, objectifs TINYTEXT NOT NULL, details LONGTEXT NOT NULL, sources LONGTEXT NOT NULL, parametres LONGTEXT NOT NULL, historique_versions LONGTEXT NOT NULL, last_update DATETIME NOT NULL, update_nb INT NOT NULL, INDEX IDX_7BB54F47421FFFC (sub_folder_id), INDEX IDX_7BB54F47369A1EE0 (main_folder_id), INDEX IDX_7BB54F47A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_dossier (id INT AUTO_INCREMENT NOT NULL, mainfolder_id INT DEFAULT NULL, nom_dossier VARCHAR(255) NOT NULL, INDEX IDX_1E41987AE1CEED57 (mainfolder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, plain_password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE report_catalog ADD CONSTRAINT FK_7BB54F47421FFFC FOREIGN KEY (sub_folder_id) REFERENCES sous_dossier (id)');
        $this->addSql('ALTER TABLE report_catalog ADD CONSTRAINT FK_7BB54F47369A1EE0 FOREIGN KEY (main_folder_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE report_catalog ADD CONSTRAINT FK_7BB54F47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sous_dossier ADD CONSTRAINT FK_1E41987AE1CEED57 FOREIGN KEY (mainfolder_id) REFERENCES dossier (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE report_catalog DROP FOREIGN KEY FK_7BB54F47369A1EE0');
        $this->addSql('ALTER TABLE sous_dossier DROP FOREIGN KEY FK_1E41987AE1CEED57');
        $this->addSql('ALTER TABLE report_catalog DROP FOREIGN KEY FK_7BB54F47421FFFC');
        $this->addSql('ALTER TABLE report_catalog DROP FOREIGN KEY FK_7BB54F47A76ED395');
        $this->addSql('DROP TABLE dossier');
        $this->addSql('DROP TABLE report_catalog');
        $this->addSql('DROP TABLE sous_dossier');
        $this->addSql('DROP TABLE user');
    }
}
