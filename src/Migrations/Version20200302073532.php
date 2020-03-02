<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200302073532 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE domaines (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', nom_dossier VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE imports (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', last_date DATETIME NOT NULL, excel_file VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liens (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', indicateur_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', matrice_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_A0A0BABCDA3B8F3D (indicateur_id), INDEX IDX_A0A0BABCBBF76425 (matrice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE LiensAxes_Objets (liens_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', referentiel_objets_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_68DE8E8C62F9273E (liens_id), INDEX IDX_68DE8E8C7289EA7E (referentiel_objets_id), PRIMARY KEY(liens_id, referentiel_objets_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matrice (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', domaine_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_4DCF5DA04272FC9F (domaine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referentiel_objets (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', nom_objet VARCHAR(255) DEFAULT NULL, schema_obj VARCHAR(255) DEFAULT NULL, tableobj VARCHAR(255) DEFAULT NULL, champ VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, qualification VARCHAR(255) DEFAULT NULL, denomination VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ref_obj_rapport (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', rapport_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', objet_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', nom_rapport VARCHAR(255) NOT NULL, nom_objet VARCHAR(255) NOT NULL, INDEX IDX_E1A36A611DFBCC46 (rapport_id), INDEX IDX_E1A36A61F520CF5A (objet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', sub_folder_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', main_folder_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', n INT DEFAULT NULL, nom_rapport VARCHAR(255) DEFAULT NULL, version_actuelle VARCHAR(255) DEFAULT NULL, commentaire LONGTEXT DEFAULT NULL, categorie VARCHAR(255) DEFAULT NULL, objectifs LONGTEXT DEFAULT NULL, details LONGTEXT DEFAULT NULL, sources LONGTEXT DEFAULT NULL, parametres LONGTEXT DEFAULT NULL, historique_versions LONGTEXT DEFAULT NULL, last_update DATETIME NOT NULL, update_nb INT NOT NULL, creation_date DATETIME DEFAULT NULL, sqltext LONGTEXT DEFAULT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_F11FA745421FFFC (sub_folder_id), INDEX IDX_F11FA745369A1EE0 (main_folder_id), INDEX IDX_F11FA745B03A8386 (created_by_id), INDEX IDX_F11FA745896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_dossier (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', mainfolder_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', nom_dossier VARCHAR(255) NOT NULL, INDEX IDX_1E41987AE1CEED57 (mainfolder_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, response_secrete VARCHAR(255) DEFAULT NULL, question_secrete VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE liens ADD CONSTRAINT FK_A0A0BABCDA3B8F3D FOREIGN KEY (indicateur_id) REFERENCES referentiel_objets (id)');
        $this->addSql('ALTER TABLE liens ADD CONSTRAINT FK_A0A0BABCBBF76425 FOREIGN KEY (matrice_id) REFERENCES matrice (id)');
        $this->addSql('ALTER TABLE LiensAxes_Objets ADD CONSTRAINT FK_68DE8E8C62F9273E FOREIGN KEY (liens_id) REFERENCES liens (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE LiensAxes_Objets ADD CONSTRAINT FK_68DE8E8C7289EA7E FOREIGN KEY (referentiel_objets_id) REFERENCES referentiel_objets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE matrice ADD CONSTRAINT FK_4DCF5DA04272FC9F FOREIGN KEY (domaine_id) REFERENCES domaines (id)');
        $this->addSql('ALTER TABLE ref_obj_rapport ADD CONSTRAINT FK_E1A36A611DFBCC46 FOREIGN KEY (rapport_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE ref_obj_rapport ADD CONSTRAINT FK_E1A36A61F520CF5A FOREIGN KEY (objet_id) REFERENCES referentiel_objets (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745421FFFC FOREIGN KEY (sub_folder_id) REFERENCES sous_dossier (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745369A1EE0 FOREIGN KEY (main_folder_id) REFERENCES domaines (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sous_dossier ADD CONSTRAINT FK_1E41987AE1CEED57 FOREIGN KEY (mainfolder_id) REFERENCES domaines (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE matrice DROP FOREIGN KEY FK_4DCF5DA04272FC9F');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745369A1EE0');
        $this->addSql('ALTER TABLE sous_dossier DROP FOREIGN KEY FK_1E41987AE1CEED57');
        $this->addSql('ALTER TABLE LiensAxes_Objets DROP FOREIGN KEY FK_68DE8E8C62F9273E');
        $this->addSql('ALTER TABLE liens DROP FOREIGN KEY FK_A0A0BABCBBF76425');
        $this->addSql('ALTER TABLE liens DROP FOREIGN KEY FK_A0A0BABCDA3B8F3D');
        $this->addSql('ALTER TABLE LiensAxes_Objets DROP FOREIGN KEY FK_68DE8E8C7289EA7E');
        $this->addSql('ALTER TABLE ref_obj_rapport DROP FOREIGN KEY FK_E1A36A61F520CF5A');
        $this->addSql('ALTER TABLE ref_obj_rapport DROP FOREIGN KEY FK_E1A36A611DFBCC46');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745421FFFC');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745B03A8386');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745896DBBDE');
        $this->addSql('DROP TABLE domaines');
        $this->addSql('DROP TABLE imports');
        $this->addSql('DROP TABLE liens');
        $this->addSql('DROP TABLE LiensAxes_Objets');
        $this->addSql('DROP TABLE matrice');
        $this->addSql('DROP TABLE referentiel_objets');
        $this->addSql('DROP TABLE ref_obj_rapport');
        $this->addSql('DROP TABLE reports');
        $this->addSql('DROP TABLE sous_dossier');
        $this->addSql('DROP TABLE user');
    }
}