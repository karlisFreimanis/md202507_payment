<?php

namespace App\Service;

use App\Dto\Api\PaymentRequestDto;
use App\Entity\Loan;
use App\Entity\Payment;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class PaymentProcessingService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(PaymentRequestDto $paymentRequestDto): Payment
    {
        $loan = $this->entityManager
            ->getRepository(Loan::class)
            ->findOneBy(['reference' => $paymentRequestDto->getRefId()]);

        if (!$loan) {
            throw new ValidatorException('Loan not found');
        }

        $currentPayment = $loan->getPayment();
        if (isset($currentPayment)) {
            throw new ValidatorException('Duplicate entry');
        }

        $payment = $this->map($paymentRequestDto, $loan);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }

    private function map(PaymentRequestDto $paymentRequestDto, Loan $loan): Payment
    {
        $payment = new Payment();
        $amountInCents = (int) bcmul($paymentRequestDto->getAmount(), '100', 0);
        $date    = DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $paymentRequestDto->getPaymentDate());
        return $payment
            ->setLoan($loan)
            ->setDescription($paymentRequestDto->getDescription())
            ->setAmount($amountInCents)
            ->setPaymentDate($date);
        //todo status missing
    }
}