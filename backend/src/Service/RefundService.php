<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Payment;
use App\Entity\PaymentOrder;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class RefundService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Autowire(service: 'monolog.logger.payment')]
        private LoggerInterface $logger,
    ) {
    }

    public function createRefundOrder(
        Payment $payment,
        int $refundAmount,
        ?Customer $customer = null,
    ): void {
        $paymentOrder = new PaymentOrder();
        $paymentOrder
            ->setPayment($payment)
            ->setAmount($refundAmount);
        if ($customer) {
            $paymentOrder->setCustomer($customer);
        }

        $this->entityManager->persist($paymentOrder);
        $this->entityManager->flush();
        $this->logger->info('PaymentOrder created', ['id' => $paymentOrder->getId()]);
    }
}