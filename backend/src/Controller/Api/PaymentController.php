<?php

namespace App\Controller\Api;

use App\Dto\Api\PaymentRequestDto;
use App\Exception\DuplicateEntryException;
use App\Service\Api\PaymentImportService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends BaseApiController
{
    #[Route(
        path: '/api/payment',
        name: 'api_payment',
        methods: ['POST'],
    )]
    public function payment(
        PaymentImportService $paymentProcessingService,
        Request $request,
    ): JsonResponse {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), PaymentRequestDto::class, 'json');
            $dtoErrors = $this->validateDtoFromRequest($dto);

            if (isset($dtoErrors)) {
                return $this->defaultErrorResponse(
                    'Invalid request.',
                    $dtoErrors,
                );
            }

            $payment = $paymentProcessingService->process($dto);
            return $this->defaultSuccessResponse(
                'Payment processed successfully.',
                [
                    'paymentId' => $payment->getId(),
                ]
            );
        } catch (DuplicateEntryException $exception) {
            return $this->defaultErrorResponse(
                $exception->getMessage(),
                [
                    'refId' => $dto->getRefId(),
                ]
            );
        } catch (\Exception $exception) {
            return $this->defaultErrorResponse(
                'Unexpected error occurred.',
                [
                    'exception' => $exception->getMessage()
                ],
            );
        }
    }
}
