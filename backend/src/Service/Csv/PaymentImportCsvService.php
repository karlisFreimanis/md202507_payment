<?php

namespace App\Service\Csv;

use App\Dto\Csv\PaymentRequestCsvDto;
use App\Mapper\PaymentMapper;
use App\Repository\PaymentRepository;
use App\Service\Api\PaymentImportApiService;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentImportCsvService
{
    private const int SUCCESS_CODE = 0;
    private const int ERROR_DUPLICATE_ENTRY = 1;
    private const int ERROR_NEGATIVE_AMOUNT = 2;
    private const int ERROR_INVALID_DATE = 3;
    private const int ERROR_UNKNOWN = 4;
    private const array ERROR_BY_FIELD_CODES = [
        'amount' => self::ERROR_NEGATIVE_AMOUNT,
        'paymentDate' => self::ERROR_INVALID_DATE,
    ];

    public const array STATUS_MESSAGES = [
        self::SUCCESS_CODE => 'All fine',
        self::ERROR_DUPLICATE_ENTRY => 'Duplicate entry',
        self::ERROR_NEGATIVE_AMOUNT => 'Negative amount',
        self::ERROR_INVALID_DATE => 'Invalid date',
        self::ERROR_UNKNOWN => 'Unknown error',
    ];

    public function __construct(
        protected readonly PaymentImportApiService $paymentImportApiService,
        protected readonly PaymentMapper $paymentMapper,
        protected readonly PaymentRepository $paymentRepository,
        protected readonly SerializerInterface $serializer,
        protected readonly ValidatorInterface $validator,
    ) {
    }

    public function process(array $row): int
    {
        try {
            $dto    = $this->serializer->denormalize($row, PaymentRequestCsvDto::class);
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                return $this->getErrorCode($errors);
            }

            $paymentId = $this->getPaymentRefIdFromName($dto->getPaymentReference());
            //theoretically I could find payments by ssn and loanNumber
            //through Customer and Loan then compare if there is duplicate by time and amount, maybe later
            $existing = $this->paymentRepository->findOneBy(['id' => $paymentId]);
            if (isset($existing)) {
                return self::ERROR_DUPLICATE_ENTRY;
            }

            $this->paymentImportApiService->process(
                $this->paymentMapper->mapCsvToApiDto($dto, $paymentId)
            );

            return self::SUCCESS_CODE;
        } catch (\Exception $exception) {
            return self::ERROR_UNKNOWN;
        }
    }

    private function getErrorCode(ConstraintViolationList $errors): int
    {
        $fields = array_keys(self::ERROR_BY_FIELD_CODES);
        foreach ($errors as $error) {
            if (in_array($error->getPropertyPath(), $fields)) {
                return self::ERROR_BY_FIELD_CODES[$error->getPropertyPath()];
            }
        }
        return self::ERROR_UNKNOWN;
    }

    //most annoying part of the homework everything is reference/refId
    private function getPaymentRefIdFromName(
        //version a
        //ReferenceRepository $referenceRepository
        //version b
        //PaymentSystemCli $paymentSystemCli
        string $code
    ): ?string {
        //version a
        //$this->paymentRepository->findOneBy(['code' => $code])->getId()
        //version b
        //return $paymentSystemCli->getRefId($name);
        $refId = Uuid::v4();
        return $refId->toRfc4122();
    }

    public function getReportMessage(SymfonyStyle $symfonyStyle, array $report): void
    {
        foreach (self::STATUS_MESSAGES as $errorCode => $message) {
            $count = $report[$errorCode] ?? 0;
            $symfonyStyle->writeln($errorCode . ' : ' . $message . ' : ' . $count);
        }
    }
}