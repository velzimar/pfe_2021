<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210228173038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE deal (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, business_id INT NOT NULL, filename VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, nom VARCHAR(30) NOT NULL, description LONGTEXT NOT NULL, prix NUMERIC(10, 3) NOT NULL, qtt INT NOT NULL, INDEX IDX_E3FEC11612469DE2 (category_id), INDEX IDX_E3FEC116A89DB457 (business_id), UNIQUE INDEX unique_dealcategory_deal (nom, category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deal_category (id INT AUTO_INCREMENT NOT NULL, business_id_id INT NOT NULL, nom VARCHAR(30) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_E5CB085B1A579E8 (business_id_id), UNIQUE INDEX unique_businessId_nom (nom, business_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC11612469DE2 FOREIGN KEY (category_id) REFERENCES deal_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC116A89DB457 FOREIGN KEY (business_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE deal_category ADD CONSTRAINT FK_E5CB085B1A579E8 FOREIGN KEY (business_id_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC11612469DE2');
        $this->addSql('DROP TABLE deal');
        $this->addSql('DROP TABLE deal_category');
    }
}
