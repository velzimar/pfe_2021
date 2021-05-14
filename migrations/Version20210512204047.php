<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210512204047 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX unique_status_selectedDate_service ON reservation');
        $this->addSql('CREATE UNIQUE INDEX unique_selectedDate_service ON reservation (selected_date, service_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX unique_selectedDate_service ON reservation');
        $this->addSql('CREATE UNIQUE INDEX unique_status_selectedDate_service ON reservation (status, selected_date, service_id)');
    }
}
