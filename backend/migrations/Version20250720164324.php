<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250720164324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loan (id VARCHAR(36) NOT NULL, customer_id VARCHAR(36) NOT NULL, reference_id VARCHAR(36) NOT NULL, amount_issued INT NOT NULL, amount_to_pay INT NOT NULL, INDEX IDX_C5D30D039395C3F3 (customer_id), UNIQUE INDEX UNIQ_C5D30D031645DEA9 (reference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D039395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D031645DEA9 FOREIGN KEY (reference_id) REFERENCES reference (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D039395C3F3');
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D031645DEA9');
        $this->addSql('DROP TABLE loan');
    }
}
