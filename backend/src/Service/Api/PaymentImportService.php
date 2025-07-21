<?php

namespace App\Service;

use App\Dto\Api\PaymentRequestDto;
use App\Entity\Loan;
use App\Entity\Payment;
use App\Exception\DuplicateEntryException;
use App\Mapper\PaymentMapper;
use App\Repository\LoanRepository;
use App\Repository\PaymentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class PaymentProcessingService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PaymentMapper $paymentMapper,
        private readonly PaymentRepository $paymentRepository,
        private readonly LoanRepository $loanRepository,
    ) {
    }

    /**
     * @param PaymentRequestDto $paymentRequestDto
     * @return Payment
     * @throws DuplicateEntryException
     */
    public function process(PaymentRequestDto $paymentRequestDto): Payment
    {
        $existing = $this->paymentRepository->findOneBy(['id' => $paymentRequestDto->getRefId()]);
        if (isset($existing)) {
            throw new DuplicateEntryException();
        }

        $loan = $this->loanRepository->findOneBy(
            [
                'loan_number' => $this->paymentMapper->extractLoanNumber($paymentRequestDto->getDescription())
            ]
        );

        if (!$loan) {
            throw new ValidatorException('Loan not found');
        }

        $payment = $this->paymentMapper->mapDtoToEntity($paymentRequestDto, $loan);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }
}