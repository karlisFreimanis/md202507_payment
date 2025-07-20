<?php

namespace App\Controller\Api;

use App\Exception\ApiValidationException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class BaseApiController extends AbstractController
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
    )
    {

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
}
