<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519140348 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE search_params_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE search_params (id INT NOT NULL, departure_point VARCHAR(3) NOT NULL, arrival_point VARCHAR(3) NOT NULL, to_departure_day VARCHAR(2) NOT NULL, to_departure_month VARCHAR(2) NOT NULL, from_departure_day VARCHAR(2) DEFAULT NULL, from_departure_month VARCHAR(2) DEFAULT NULL, reservation_class VARCHAR(1) DEFAULT NULL, adults INT NOT NULL, children INT DEFAULT NULL, infants INT DEFAULT NULL, show_more_clicks INT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE search_params_id_seq CASCADE');
        $this->addSql('DROP TABLE search_params');
    }
}
