<?php

namespace App\Service;

use App\Entity\Loan;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class PaymentAssignmentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RefundService $refundService,
        #[Autowire(service: 'monolog.logger.payment')]
        private LoggerInterface $logger,
    ) {
    }

    public function assignPayment(Payment $payment): void
    {
        $loan = $payment->getLoans();

        if (!$loan) {
            $this->handleUnassignedLoan($payment);
            return;
        }

        $unPaidAmount = $this->calculateUnPaidAmount($loan, $payment->getAmount());

        $this->handlePaymentAssignment($payment, $loan, $unPaidAmount);

        $this->entityManager->persist($payment);
        $this->entityManager->persist($loan);
        $this->entityManager->flush();

        if ($payment->isAssigned()) {
            return;
        }

        $this->refundService->createRefundOrder($payment, abs($unPaidAmount), $loan->getCustomer());
    }

    private function handleUnassignedLoan(Payment $payment): void
    {
        $this->logger->critical('Loan not assigned', ['paymentId' => $payment->getId()]);
        $this->markPaymentAsPartiallyAssigned($payment);
        $this->refundService->createRefundOrder($payment, $payment->getAmount());
    }

    public function calculateUnPaidAmount(Loan $loan, int $paymentAmount): int
    {
        if ($loan->isPaid()) {
            $this->logger->error('Paid loan received payment', ['loanId' => $loan->getId()]);
            return -$paymentAmount;
        }

        $amountPaid = 0;
        foreach ($loan->getPayments() as $payment) {
            if (!$payment->isAssigned()) {
                continue;
            }
            $amountPaid += $payment->getAmount();
        }

        return $loan->getAmountToPay() - $amountPaid - $paymentAmount;
    }

    private function handlePaymentAssignment(Payment $payment, Loan $loan, int $unPaidAmount): void
    {
        if ($unPaidAmount === 0) {
            $this->markLoanAsPaid($loan);
            $this->markPaymentAsAssigned($payment);
        } elseif ($unPaidAmount > 0) {
            $this->markPaymentAsAssigned($payment);
        } else {
            $this->markLoanAsPaid($loan);
            $this->markPaymentAsPartiallyAssigned($payment);
        }
    }

    private function markPaymentAsPartiallyAssigned(Payment $payment): void
    {
        $payment->setIsAssigned(false);
        $this->logger->info('Payment partially assigned', ['paymentId' => $payment->getId()]);
    }

    private function markPaymentAsAssigned(Payment $payment): void
    {
        $payment->setIsAssigned(true);
        $this->logger->info('Payment is assigned', ['paymentId' => $payment->getId()]);
    }

    private function markLoanAsPaid(Loan $loan): void
    {
        $loan->setIsPaid(true);
        $this->logger->info('Loan is paid', ['loanId' => $loan->getId()]);
    }
}