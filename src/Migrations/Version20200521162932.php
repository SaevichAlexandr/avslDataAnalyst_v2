<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200521162932 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE raw_data DROP CONSTRAINT fk_864547c9b27db54b');
        $this->addSql('DROP INDEX idx_864547c9b27db54b');
        $this->addSql('ALTER TABLE raw_data RENAME COLUMN search_params_id_id TO search_params_id');
        $this->addSql('ALTER TABLE raw_data ADD CONSTRAINT FK_864547C98887DAA7 FOREIGN KEY (search_params_id) REFERENCES search_params (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_864547C98887DAA7 ON raw_data (search_params_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE raw_data DROP CONSTRAINT FK_864547C98887DAA7');
        $this->addSql('DROP INDEX IDX_864547C98887DAA7');
        $this->addSql('ALTER TABLE raw_data RENAME COLUMN search_params_id TO search_params_id_id');
        $this->addSql('ALTER TABLE raw_data ADD CONSTRAINT fk_864547c9b27db54b FOREIGN KEY (search_params_id_id) REFERENCES search_params (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_864547c9b27db54b ON raw_data (search_params_id_id)');
    }
}
