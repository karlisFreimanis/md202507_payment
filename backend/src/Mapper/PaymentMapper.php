<?php

namespace App\Mapper;

use App\Dto\Api\PaymentRequestDto;
use App\Entity\Loan;
use App\Entity\Payment;
use DateTimeImmutable;

class PaymentMapper
{
    public function mapDtoToEntity(PaymentRequestDto $dto, ?Loan $loan): Payment
    {
        $amountInCents = (int)bcmul($dto->getAmount(), '100', 0);

        $date = DateTimeImmutable::createFromFormat(
            \DateTimeInterface::ATOM,
            $dto->getPaymentDate()
        );

        return new Payment()
            ->setId($dto->getRefId())
            ->setLoans($loan)
            ->setDescription($dto->getDescription())
            ->setAmount($amountInCents)
            ->setPaymentDate($date)
            ->setIsAssigned(false);
    }

    public function extractLoanNumber(string $text): ?string
    {
        if (preg_match('/\bLN\d{8}\b/', $text, $matches)) {
            return $matches[0];
        }

        return null;
    }
}

