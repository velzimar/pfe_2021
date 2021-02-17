<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210217183150 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73561A579E8');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73561A579E8 FOREIGN KEY (business_id_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_category DROP FOREIGN KEY FK_CDFC73561A579E8');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73561A579E8 FOREIGN KEY (business_id_id) REFERENCES user (id)');
    }
}
