<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200524094558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE suppliers_price (supplier_id INT NOT NULL, offer_data_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(supplier_id, offer_data_id))');
        $this->addSql('CREATE INDEX IDX_938F4F792ADD6D8C ON suppliers_price (supplier_id)');
        $this->addSql('CREATE INDEX IDX_938F4F7996FD6735 ON suppliers_price (offer_data_id)');
        $this->addSql('ALTER TABLE suppliers_price ADD CONSTRAINT FK_938F4F792ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE suppliers_price ADD CONSTRAINT FK_938F4F7996FD6735 FOREIGN KEY (offer_data_id) REFERENCES offer_data (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_238b6b19ad51911c RENAME TO IDX_238B6B1996FD6735');
        $this->addSql('ALTER INDEX idx_238b6b19ac1354f1 RENAME TO IDX_238B6B1991F478C5');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE suppliers_price');
        $this->addSql('ALTER INDEX idx_238b6b1996fd6735 RENAME TO idx_238b6b19ad51911c');
        $this->addSql('ALTER INDEX idx_238b6b1991f478c5 RENAME TO idx_238b6b19ac1354f1');
    }
}
