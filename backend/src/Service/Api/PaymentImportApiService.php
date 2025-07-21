<?php

namespace App\Service\Api;

use App\Dto\Api\PaymentRequestApiDto;
use App\Entity\Payment;
use App\Exception\DuplicateEntryException;
use App\Mapper\PaymentMapper;
use App\Repository\LoanRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class PaymentImportApiService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaymentMapper $paymentMapper,
        private PaymentRepository $paymentRepository,
        private LoanRepository $loanRepository,
    ) {
    }

    /**
     * @param PaymentRequestApiDto $paymentRequestDto
     * @return Payment
     * @throws DuplicateEntryException
     */
    public function process(PaymentRequestApiDto $paymentRequestDto): Payment
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

        $payment = $this->paymentMapper->mapDtoToEntity($paymentRequestDto, $loan);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }
}