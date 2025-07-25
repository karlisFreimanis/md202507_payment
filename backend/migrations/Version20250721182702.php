<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721182702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_order (id INT AUTO_INCREMENT NOT NULL, customer_id VARCHAR(36) DEFAULT NULL, payment_id VARCHAR(36) NOT NULL, amount INT NOT NULL, INDEX IDX_A260A52A9395C3F3 (customer_id), UNIQUE INDEX UNIQ_A260A52A4C3A3BB (payment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment_order ADD CONSTRAINT FK_A260A52A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE payment_order ADD CONSTRAINT FK_A260A52A4C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_order DROP FOREIGN KEY FK_A260A52A9395C3F3');
        $this->addSql('ALTER TABLE payment_order DROP FOREIGN KEY FK_A260A52A4C3A3BB');
        $this->addSql('DROP TABLE payment_order');
    }
}
