<?php

namespace App\Controller\Api;

use App\Dto\DtoInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseApiController extends AbstractController
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
        protected readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @OA\Info(title="My First API", version="0.1")
     */

    /**
     * @OA\Get(
     *     path="/api/resource.json",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */

    #[OA\Get(
        path: '/api/ping',
        operationId: 'ping',
        description: 'Pings the service',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Return ping',
                content: new OA\JsonContent(
                              type: 'string',
                              example: 'ping',
                          ),
            ),
        ]
    )]
    #[Route(
        path: '/api/ping',
        name: 'api_ping',
        methods: ['GET'],
    )]
    /**
     * @return JsonResponse
     */
    final public function ping(): JsonResponse
    {
        return new JsonResponse(['ping' => true]);
    }

    public function validateDtoFromRequest(
        DtoInterface $dto,
    ): ?array {
        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $field          = $violation->getPropertyPath();
                $errors[$field] = $violation->getMessage();
            }

            return $errors;
        }

        return null;
    }

    final protected function defaultErrorResponse(
        string $message,
        array $errors,
        int $status = Response::HTTP_BAD_REQUEST,
    ): JsonResponse {
        return new JsonResponse(
            [
                'status' => $status,
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ],
            $status,
        );
    }

    final protected function defaultSuccessResponse(
        string $message,
        array $data,
    ): JsonResponse {
        return new JsonResponse(
            [
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => $message,
                'data' => $data,
            ],
            Response::HTTP_OK,
        );
    }
}
