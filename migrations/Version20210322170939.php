<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322170939 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_calendar DROP FOREIGN KEY FK_97DFFA7BED5CA9E6');
        $this->addSql('DROP INDEX UNIQ_97DFFA7BED5CA9E6 ON service_calendar');
        $this->addSql('ALTER TABLE service_calendar CHANGE service_id service INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service_calendar ADD CONSTRAINT FK_97DFFA7BE19D9AD2 FOREIGN KEY (service) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97DFFA7BE19D9AD2 ON service_calendar (service)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_calendar DROP FOREIGN KEY FK_97DFFA7BE19D9AD2');
        $this->addSql('DROP INDEX UNIQ_97DFFA7BE19D9AD2 ON service_calendar');
        $this->addSql('ALTER TABLE service_calendar CHANGE service service_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service_calendar ADD CONSTRAINT FK_97DFFA7BED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97DFFA7BED5CA9E6 ON service_calendar (service_id)');
    }
}
