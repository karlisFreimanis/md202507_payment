<?php

namespace App\Mapper;

use App\Dto\Api\PaymentRequestApiDto;
use App\Dto\Csv\PaymentRequestCsvDto;
use App\Entity\Loan;
use App\Entity\Payment;
use DateTimeImmutable;

class PaymentMapper
{
    public function mapDtoToEntity(PaymentRequestApiDto $dto, ?Loan $loan): Payment
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

    public function mapCsvToApiDto(PaymentRequestCsvDto $csvDto, string $paymentId): PaymentRequestApiDto
    {
        $apiDto = new PaymentRequestApiDto();

        $apiDto->setFirstname($csvDto->getPayerName());
        $apiDto->setLastname($csvDto->getPayerSurname());
        $apiDto->setAmount($csvDto->getAmount());
        $apiDto->setDescription($csvDto->getDescription());

        // Convert paymentDate format from 'YmdHis' to ISO 8601
        $date = \DateTimeImmutable::createFromFormat('YmdHis', $csvDto->getPaymentDate());
        $apiDto->setPaymentDate($date?->format(\DateTimeInterface::ATOM) ?? '');

        $apiDto->setRefId($paymentId);

        return $apiDto;
    }

}

