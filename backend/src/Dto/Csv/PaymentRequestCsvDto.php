<?php

namespace App\Dto\Csv;

use App\Dto\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestCsvDto implements DtoInterface
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{14}$/',
        message: 'paymentDate must be in the format YYYYMMDDHHMMSS.'
    )]
    private string $paymentDate;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $payerName;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $payerSurname;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Amount must be a valid decimal number with up to 2 decimal places.'
    )]
    #[Assert\Positive]
    private string $amount;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $nationalSecurityNumber;

    #[Assert\NotBlank]
    #[Assert\Length(max: 1024)]
    private string $description;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $paymentReference;

    // Getters and setters

    public function getPaymentDate(): string
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(string $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    public function getPayerName(): string
    {
        return $this->payerName;
    }

    public function setPayerName(string $payerName): void
    {
        $this->payerName = $payerName;
    }

    public function getPayerSurname(): string
    {
        return $this->payerSurname;
    }

    public function setPayerSurname(string $payerSurname): void
    {
        $this->payerSurname = $payerSurname;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getNationalSecurityNumber(): string
    {
        return $this->nationalSecurityNumber;
    }

    public function setNationalSecurityNumber(string $nationalSecurityNumber): void
    {
        $this->nationalSecurityNumber = $nationalSecurityNumber;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPaymentReference(): string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(string $paymentReference): void
    {
        $this->paymentReference = $paymentReference;
    }
}
