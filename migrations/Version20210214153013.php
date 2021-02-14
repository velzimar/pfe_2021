<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210214153013 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_category (id INT AUTO_INCREMENT NOT NULL, business_id INT NOT NULL, nom VARCHAR(30) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_CDFC73561A579E8 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_category ADD CONSTRAINT FK_CDFC73561A579E8 FOREIGN KEY (business_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user RENAME INDEX categoryid TO IDX_8D93D649D36A08A1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product_category');
        $this->addSql('ALTER TABLE `user` RENAME INDEX idx_8d93d649d36a08a1 TO categoryId');
    }
}
