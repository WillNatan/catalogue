<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200218130041 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE LiensAxes_Objets (liens_id INT NOT NULL, referentiel_objets_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_68DE8E8C62F9273E (liens_id), INDEX IDX_68DE8E8C7289EA7E (referentiel_objets_id), PRIMARY KEY(liens_id, referentiel_objets_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE LiensAxes_Objets ADD CONSTRAINT FK_68DE8E8C62F9273E FOREIGN KEY (liens_id) REFERENCES liens (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE LiensAxes_Objets ADD CONSTRAINT FK_68DE8E8C7289EA7E FOREIGN KEY (referentiel_objets_id) REFERENCES referentiel_objets (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE liens_referentiel_objets');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE liens_referentiel_objets (liens_id INT NOT NULL, referentiel_objets_id CHAR(36) NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', INDEX IDX_B4A9A21662F9273E (liens_id), INDEX IDX_B4A9A2167289EA7E (referentiel_objets_id), PRIMARY KEY(liens_id, referentiel_objets_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE liens_referentiel_objets ADD CONSTRAINT FK_B4A9A21662F9273E FOREIGN KEY (liens_id) REFERENCES liens (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE liens_referentiel_objets ADD CONSTRAINT FK_B4A9A2167289EA7E FOREIGN KEY (referentiel_objets_id) REFERENCES referentiel_objets (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE LiensAxes_Objets');
    }
}
