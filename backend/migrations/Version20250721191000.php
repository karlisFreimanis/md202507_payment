<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250721191000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert 10 test customers, loans, and payments';
    }

    public function up(Schema $schema): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $customerId = sprintf('cust-uuid-%02d', $i);
            $loanId     = sprintf('loan-uuid-%02d', $i);
            $paymentId  = sprintf('pay-uuid-%02d', $i);
            $ssn        = str_pad((string)(1234567890 + $i), 10, '0', STR_PAD_LEFT);
            $loanNumber = 'LN' . str_pad((string)(72638000 + $i), 8, '0', STR_PAD_LEFT);
            $description = $loanNumber;
            $email      = "user$i@example.com";
            $phone      = "555000$i";

            $this->addSql(<<<SQL
                INSERT INTO customer (id, first_name, last_name, ssn, email, phone)
                VALUES (
                    '$customerId',
                    'First$i',
                    'Last$i',
                    '$ssn',
                    '$email',
                    '$phone'
                )
            SQL);

            $this->addSql(<<<SQL
                INSERT INTO loan (id, customer_id, amount_issued, amount_to_pay, loan_number, is_paid)
                VALUES (
                    '$loanId',
                    '$customerId',
                    100000,
                    100000,
                    '$loanNumber',
                    0
                )
            SQL);

            $this->addSql(<<<SQL
                INSERT INTO payment (id, loans_id, payment_date, amount, description, is_assigned)
                VALUES (
                    '$paymentId',
                    '$loanId',
                    '2025-07-21 12:00:00',
                    10000,
                    '$description',
                    1
                )
            SQL);
        }
    }

    public function down(Schema $schema): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $customerId = sprintf('cust-uuid-%02d', $i);
            $loanId     = sprintf('loan-uuid-%02d', $i);
            $paymentId  = sprintf('pay-uuid-%02d', $i);

            $this->addSql("DELETE FROM payment WHERE id = '$paymentId'");
            $this->addSql("DELETE FROM loan WHERE id = '$loanId'");
            $this->addSql("DELETE FROM customer WHERE id = '$customerId'");
        }
    }
}
