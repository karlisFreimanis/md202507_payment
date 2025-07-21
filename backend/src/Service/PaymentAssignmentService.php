<?php

namespace App\Service;

use App\Entity\Loan;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

readonly class PaymentAssignmentService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RefundService $refundService,
    ) {
    }

    public function assignPayment(Payment $payment): void
    {
        $loan = $payment->getLoans();
        if (!isset($loan)) {
            $this->refundService->createRefundOrder($payment, $payment->getAmount());
            return;
        }

        $unPaidAmount = $this->getUnPaidAmount($loan, $payment->getAmount());
        if ($unPaidAmount === 0) {
            $this->markLoanAsPaid($loan);
            $this->markPaymentAsAssigned($payment);
        } elseif ($unPaidAmount > 0) {
            $this->markPaymentAsAssigned($payment);
        } elseif ($unPaidAmount < 0) {
            $this->markLoanAsPaid($loan);
            $this->markPaymentAsPartiallyAssigned($payment);
        }

        $this->entityManager->persist($payment);
        $this->entityManager->persist($loan);
        $this->entityManager->flush();

        if ($payment->isAssigned()) {
            return;
        }
        $this->refundService->createRefundOrder($payment, abs($unPaidAmount), $loan->getCustomer());
    }

    private function markPaymentAsPartiallyAssigned($payment): void
    {
        $payment->setIsAssigned(false);
    }

    private function markPaymentAsAssigned(Payment $payment): void
    {
        $payment->setIsAssigned(true);
    }

    private function markLoanAsPaid(Loan $loan): void
    {
        $loan->setIsPaid(true);
    }

    private function getUnPaidAmount(
        Loan $loan,
        int $paymentAmount,
    ): int {
        if ($loan->isPaid()) {
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
}