<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200219055726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE liens CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', CHANGE matrice_id matrice_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE liensaxes_objets CHANGE liens_id liens_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE matrice CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE liens CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE matrice_id matrice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE LiensAxes_Objets CHANGE liens_id liens_id INT NOT NULL');
        $this->addSql('ALTER TABLE matrice CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
