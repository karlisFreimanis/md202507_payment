<?php

namespace App\Controller\Api;

use OpenApi\Attributes as OA;

use App\Dto\Api\PaymentRequestApiDto;
use App\Exception\DuplicateEntryException;
use App\Service\Api\PaymentImportApiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends BaseApiController
{

    #[OA\Post(
        path: '/api/payment',
        operationId: 'processPayment',
        summary: 'Processes a payment and returns payment ID or error',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['firstname', 'lastname', 'paymentDate', 'amount', 'description', 'refId'],
                properties: [
                    new OA\Property(property: 'firstname', type: 'string', example: 'John'),
                    new OA\Property(property: 'lastname', type: 'string', example: 'Doe'),
                    new OA\Property(property: 'paymentDate', type: 'string', format: 'date-time', example: '2025-07-21T12:00:00+00:00'),
                    new OA\Property(property: 'amount', type: 'string', example: '99.99'),
                    new OA\Property(property: 'description', type: 'string', example: 'LN12345678'),
                    new OA\Property(property: 'refId', type: 'string', format: 'uuid', example: '9a9cabe9-2a75-4c66-a4dd-690c6d79e6f7'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment processed successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 200),
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment processed successfully.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'paymentId', type: 'string', format: 'uuid', example: '9a9cabe9-2a75-4c66-a4dd-690c6d79e6f7')
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Duplicate entry',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'integer', example: 409),
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Duplicate entry.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'refId',
                                    type: 'string',
                                    format: 'uuid',
                                    example: '9a9cabe9-2a75-4c66-a4dd-690c6d79e6f7'
                                )
                            ]
                        )
                    ]
                )
            )
        ])
    ]
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
