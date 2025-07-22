<?php

namespace App\Tests\Service;

use App\Entity\Customer;
use App\Entity\Loan;
use App\Entity\Payment;
use App\Service\PaymentAssignmentService;
use App\Service\RefundService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PaymentAssignmentServiceTest extends TestCase
{
    private PaymentAssignmentService $service;
    private EntityManagerInterface&MockObject $entityManager;
    private RefundService&MockObject $refundService;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->refundService = $this->createMock(RefundService::class);
        $logger              = $this->createMock(LoggerInterface::class);

        $this->service = new PaymentAssignmentService(
            $this->entityManager,
            $this->refundService,
            $logger
        );
    }

    /**
     * @dataProvider assignPaymentProvider
     */
    public function testAssignPayment(
        ?Loan $loan,
        int $paymentAmount,
        bool $expectRefund,
        bool $expectAssigned,
    ): void {
        $payment = new Payment();
        $payment->setAmount($paymentAmount);
        $payment->setLoans($loan);
        $payment->setIsAssigned(null);

        $loan?->addPayment($payment);

        if ($expectRefund) {
            $this->refundService
                ->expects($this->once())
                ->method('createRefundOrder');
        } else {
            $this->refundService
                ->expects($this->never())
                ->method('createRefundOrder');
        }

        $this->service->assignPayment($payment);
        $this->assertSame($expectAssigned, $payment->isAssigned());
    }

    public static function assignPaymentProvider(): array
    {
        // Paid loan
        $paidLoan = new Loan();
        $paidLoan->setIsPaid(true);
        $paidLoan->setAmountToPay(1000);

        // Unpaid loan
        $unpaidLoan = new Loan();
        $unpaidLoan->setIsPaid(false);
        $unpaidLoan->setAmountToPay(1000);

        $customer = new Customer();
        $unpaidLoan->setCustomer($customer);

        return [
            'no loan - refund no assigned' => [
                null,
                1000,
                true,
                false,
            ],
            'Paid loan refund no assigned' => [
                self::buildLoan(true, 1000),
                1000,
                true,
                false,
            ],
            'exact match - no refund assigned' => [
                self::buildLoan(false, 1000),
                1000,
                false,
                true,
            ],
            'underpaid - no refund assigned' => [
                self::buildLoan(false, 1000),
                500,
                false,
                true,
            ],
            'overpayment - refund no assigned' => [
                self::buildLoan(false, 1000),
                1500,
                true,
                false,
            ],
        ];
    }

    private static function buildLoan(bool $isPaid, int $amountToPay): Loan
    {
        $loan = new Loan();
        $loan->setIsPaid($isPaid);
        $loan->setAmountToPay($amountToPay);

        $customer = new Customer();
        $loan->setCustomer($customer);

        return $loan;
    }
}
