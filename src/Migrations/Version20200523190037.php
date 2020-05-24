<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200523190037 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE offer_data_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE offer_data (id INT NOT NULL, raw_data_id INT NOT NULL, akassa_price DOUBLE PRECISION NOT NULL, button_price DOUBLE PRECISION NOT NULL, akassa_href TEXT DEFAULT NULL, baggage VARCHAR(4) NOT NULL, departure_point VARCHAR(3) NOT NULL, arrival_point VARCHAR(3) NOT NULL, departure_datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, arrival_datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, transfer_time VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_530BDD7441B104A4 ON offer_data (raw_data_id)');
        $this->addSql('ALTER TABLE offer_data ADD CONSTRAINT FK_530BDD7441B104A4 FOREIGN KEY (raw_data_id) REFERENCES raw_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE offer_data_id_seq CASCADE');
        $this->addSql('DROP TABLE offer_data');
    }
}
