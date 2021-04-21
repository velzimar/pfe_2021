<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421005215 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_deal ADD business_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01A89DB457 FOREIGN KEY (business_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_AE0FFB01A89DB457 ON order_deal (business_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_deal DROP FOREIGN KEY FK_AE0FFB01A89DB457');
        $this->addSql('DROP INDEX IDX_AE0FFB01A89DB457 ON order_deal');
        $this->addSql('ALTER TABLE order_deal DROP business_id');
    }
}
