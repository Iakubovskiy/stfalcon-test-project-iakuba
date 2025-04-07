<?php
declare(strict_types=1);


namespace App\Controller\PropertyActions;

use App\DTO\ChangeStatusDTO;
use App\Presenters\PropertyPresenter;
use App\Services\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ChangePropertyStatusAction extends AbstractController
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly PropertyPresenter $propertyPresenter,
    )
    {}

    #[Route('api/properties/{id}/status', name: 'property_change_status', methods: ['PATCH'])]
    #[OA\Patch(
        description: "Updates the status of a specific property using status ID.",
        summary: "Change the status of a property",
        requestBody: new OA\RequestBody(
            description: "Updated data for the property",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "statusId", type: "string", example: "draft")
                ],
                type: "object"
            )
        ),
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Property UUID', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Property status changed successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440003'),
                        new OA\Property(property: 'status', properties: [
                            new OA\Property(property: 'id', type: 'string', example: 'available'),
                            new OA\Property(property: 'name', type: 'string', example: 'Available')
                        ], type: 'object')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid UUID format, invalid status transition, or status ID does not exist"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to change status for this property"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function propertyChangeStatus(Uuid $id, #[MapRequestPayload] ChangeStatusDTO $changeStatusDTO): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->present($this->propertyService->changePropertyStatus($id, $changeStatusDTO->statusId)),
            Response::HTTP_OK,
            []
        );
    }
}
