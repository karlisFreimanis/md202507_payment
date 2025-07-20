<?php

namespace App\Controller\Api;

use App\Dto\Api\PaymentRequestDto;
use App\Service\PaymentProcessingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentController extends BaseApiController
{
    #[Route(
        path: '/api/payment',
        name: 'api_payment',
        methods: ['POST'],
    )]
    public function payment(
        PaymentProcessingService $paymentProcessingService,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ): JsonResponse {
        $dto = $serializer->deserialize($request->getContent(), PaymentRequestDto::class, 'json');

        $violations = $validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $field = $violation->getPropertyPath();
                $errors[$field] = $violation->getMessage();
            }

            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
                'success' => false,
                'error' => 'Bad Request',
                'message' => 'Validation failed.',
                'errors' => $errors,
            ], Response::HTTP_BAD_REQUEST);
        }


        try {
            $payment = $paymentProcessingService->process($dto);
        } catch (ValidatorException $exception) {
            //todo 409
            return new JsonResponse([]);
        } catch (\Exception $exception) {
            //todo
            return new JsonResponse([]);
        }

        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'success' => true,
            'message' => 'Payment processed successfully.',
            'data' => [
                'paymentId' => $payment->getId(),
            ],
        ], Response::HTTP_OK);
    }
}