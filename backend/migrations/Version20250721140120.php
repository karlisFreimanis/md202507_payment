<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721140120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id VARCHAR(36) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, ssn VARCHAR(10) NOT NULL, email VARCHAR(254) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE loan (id VARCHAR(36) NOT NULL, customer_id VARCHAR(36) NOT NULL, amount_issued INT NOT NULL, amount_to_pay INT NOT NULL, loan_number VARCHAR(10) NOT NULL, is_paid TINYINT(1) NOT NULL, INDEX IDX_C5D30D039395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id VARCHAR(36) NOT NULL, loans_id VARCHAR(36) DEFAULT NULL, payment_date DATETIME NOT NULL COMMENT \'(DC2Type:datetimetz_immutable)\', amount INT NOT NULL, description VARCHAR(255) DEFAULT NULL, is_assigned TINYINT(1) DEFAULT NULL, INDEX IDX_6D28840D9AB85012 (loans_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE loan ADD CONSTRAINT FK_C5D30D039395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9AB85012 FOREIGN KEY (loans_id) REFERENCES loan (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan DROP FOREIGN KEY FK_C5D30D039395C3F3');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9AB85012');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE loan');
        $this->addSql('DROP TABLE payment');
    }
}
