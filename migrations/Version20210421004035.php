<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421004035 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_deal (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, deal_id INT NOT NULL, code VARCHAR(8) NOT NULL, INDEX IDX_AE0FFB01A76ED395 (user_id), INDEX IDX_AE0FFB01F60E2305 (deal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE order_deal');
    }
}
