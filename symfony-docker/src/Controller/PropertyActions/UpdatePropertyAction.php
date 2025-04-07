<?php
declare(strict_types=1);


namespace App\Controller\PropertyActions;

use App\DTO\PropertyUpdateDto;
use App\Presenters\PropertyPresenter;
use App\Services\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class UpdatePropertyAction extends AbstractController
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly PropertyPresenter $propertyPresenter,
    )
    {}

    #[Route('api/properties/{id}', name: 'property_update', methods: ['PUT'])]
    #[OA\Put(
        description: "Modifies the details of an existing property.",
        summary: "Update an existing property",
        requestBody: new OA\RequestBody(
            description: "Updated data for the property",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PropertyUpdateDto::class))
        ),
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Property UUID to update', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Property updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440003'), // ID оновленого
                        new OA\Property(property: 'type', properties: [ /* ... */ ], type: 'object'),
                        new OA\Property(property: 'price', properties: [ /* ... */ ], type: 'object'),
                        new OA\Property(property: 'status', properties: [ /* ... */ ], type: 'object')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid data provided (validation failed) or invalid UUID"), // Або 422
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to update this property"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function propertyUpdate(
        #[MapRequestPayload] PropertyUpdateDto $propertyUpdateDto,
        Uuid $id,
    ):JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->present($this->propertyService->updateProperty($id, $propertyUpdateDto)),
            Response::HTTP_OK,
            [],
        );
    }
}
