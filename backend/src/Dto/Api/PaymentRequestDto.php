<?php

namespace App\Dto\Api;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $firstname;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $lastname;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public string $paymentDate;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Amount must be a valid decimal number with up to 2 decimal places.'
    )]
    #[Assert\Positive(message: 'Amount must be greater than zero.')]
    public string $amount;

    #[Assert\NotBlank]
    #[Assert\Length(max: 1024)]
    public string $description;

    #[Assert\NotBlank]
    #[Assert\Uuid(message: 'refId must be a valid UUID.')]
    private string $refId;

    // Getters and setters

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getPaymentDate(): string
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(string $paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getRefId(): string
    {
        return $this->refId;
    }

    public function setRefId(string $refId): void
    {
        $this->refId = $refId;
    }
}
