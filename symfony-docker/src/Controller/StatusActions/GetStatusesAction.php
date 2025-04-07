<?php
declare(strict_types=1);


namespace App\Controller\StatusActions;

use App\Presenters\PropertyStatusPresenter;
use App\Services\StatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class GetStatusesAction extends AbstractController
{
    public function __construct(
        private readonly StatusService $statusService,
        private readonly PropertyStatusPresenter $propertyStatusPresenter,
    )
    {}

    #[Route('api/statuses', name: 'status', methods: ['GET'])]
    #[OA\Get(
        description: "Retrieves all available entity statuses.",
        summary: "Get list of statuses",
        tags: ["Statuses"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of statuses",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", description: "Status identifier", type: "string", example: "PENDING"),
                            new OA\Property(property: "name", description: "Status name", type: "string", example: "Pending")
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function getStatuses(): JsonResponse
    {
        return new JsonResponse(
            $this->propertyStatusPresenter->presentArray($this->statusService->getAllStatuses()),
            Response::HTTP_OK,
            [],
        );
    }
}
