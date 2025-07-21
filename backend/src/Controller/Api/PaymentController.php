<?php

namespace App\Controller\Api;

use App\Dto\Api\PaymentRequestApiDto;
use App\Exception\DuplicateEntryException;
use App\Service\Api\PaymentImportApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends BaseApiController
{
    #[Route(
        path: '/api/payment',
        name: 'api_payment',
        methods: ['POST'],
    )]
    public function payment(
        PaymentImportApiService $paymentProcessingService,
        Request $request,
    ): JsonResponse {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), PaymentRequestApiDto::class, 'json');
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
                ],
                Response::HTTP_CONFLICT,
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
