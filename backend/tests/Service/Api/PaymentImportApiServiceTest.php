<?php

namespace App\Tests\Service\Api;

use App\Dto\Api\PaymentRequestApiDto;
use App\Entity\Loan;
use App\Entity\Payment;
use App\Exception\DuplicateEntryException;
use App\Mapper\PaymentMapper;
use App\Repository\LoanRepository;
use App\Repository\PaymentRepository;
use App\Service\Api\PaymentImportApiService;
use App\Service\PaymentAssignmentService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class PaymentImportApiServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testProcessThrowsDuplicateEntryException(): void
    {
        // Arrange
        $dto = $this->createMock(PaymentRequestApiDto::class);
        $dto->method('getRefId')->willReturn('existing-id');

        $existingPayment = $this->createMock(Payment::class);

        $paymentRepository = $this->createMock(PaymentRepository::class);
        $paymentRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 'existing-id'])
            ->willReturn($existingPayment);

        // No other services should be called
        $loanRepository = $this->createMock(LoanRepository::class);
        $paymentMapper = $this->createMock(PaymentMapper::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $paymentAssignmentService = $this->createMock(PaymentAssignmentService::class);

        $service = new PaymentImportApiService(
            $entityManager,
            $paymentAssignmentService,
            $paymentMapper,
            $paymentRepository,
            $loanRepository
        );

        $this->expectException(DuplicateEntryException::class);

        // Act
        $service->process($dto);
    }
}
