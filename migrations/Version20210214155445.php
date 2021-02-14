<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210214155445 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73561A579E8');
        $this->addSql('DROP INDEX business_id ON product_category');
        $this->addSql('DROP INDEX IDX_CDFC73561A579E8 ON product_category');
        $this->addSql('ALTER TABLE product_category CHANGE business_id business_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73561A579E8 FOREIGN KEY (business_id_id) REFERENCES `user` (id)');
        $this->addSql('CREATE UNIQUE INDEX unique_businessId_nom ON product_category (nom, business_id_id)');
        $this->addSql('CREATE INDEX IDX_CDFC73561A579E8 ON product_category (business_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73561A579E8');
        $this->addSql('DROP INDEX unique_businessId_nom ON product_category');
        $this->addSql('DROP INDEX IDX_CDFC73561A579E8 ON product_category');
        $this->addSql('ALTER TABLE product_category CHANGE business_id_id business_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73561A579E8 FOREIGN KEY (business_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX business_id ON product_category (business_id, nom)');
        $this->addSql('CREATE INDEX IDX_CDFC73561A579E8 ON product_category (business_id)');
    }
}
