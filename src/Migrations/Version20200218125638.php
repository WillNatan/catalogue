<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200218125638 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE liens (id INT AUTO_INCREMENT NOT NULL, indicateur_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_A0A0BABCDA3B8F3D (indicateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liens_referentiel_objets (liens_id INT NOT NULL, referentiel_objets_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_B4A9A21662F9273E (liens_id), INDEX IDX_B4A9A2167289EA7E (referentiel_objets_id), PRIMARY KEY(liens_id, referentiel_objets_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matrice (id INT AUTO_INCREMENT NOT NULL, domaine_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_4DCF5DA04272FC9F (domaine_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE liens ADD CONSTRAINT FK_A0A0BABCDA3B8F3D FOREIGN KEY (indicateur_id) REFERENCES referentiel_objets (id)');
        $this->addSql('ALTER TABLE liens_referentiel_objets ADD CONSTRAINT FK_B4A9A21662F9273E FOREIGN KEY (liens_id) REFERENCES liens (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE liens_referentiel_objets ADD CONSTRAINT FK_B4A9A2167289EA7E FOREIGN KEY (referentiel_objets_id) REFERENCES referentiel_objets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE matrice ADD CONSTRAINT FK_4DCF5DA04272FC9F FOREIGN KEY (domaine_id) REFERENCES dossier (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE liens_referentiel_objets DROP FOREIGN KEY FK_B4A9A21662F9273E');
        $this->addSql('DROP TABLE liens');
        $this->addSql('DROP TABLE liens_referentiel_objets');
        $this->addSql('DROP TABLE matrice');
    }
}
