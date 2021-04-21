<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421004505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_deal DROP FOREIGN KEY FK_AE0FFB01A76ED395');
        $this->addSql('ALTER TABLE order_deal DROP FOREIGN KEY FK_AE0FFB01F60E2305');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX unique_user_deal ON order_deal (user_id, deal_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_deal DROP FOREIGN KEY FK_AE0FFB01A76ED395');
        $this->addSql('ALTER TABLE order_deal DROP FOREIGN KEY FK_AE0FFB01F60E2305');
        $this->addSql('DROP INDEX unique_user_deal ON order_deal');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_deal ADD CONSTRAINT FK_AE0FFB01F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id)');
    }
}
