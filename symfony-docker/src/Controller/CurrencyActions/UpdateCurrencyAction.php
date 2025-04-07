<?php
declare(strict_types=1);


namespace App\Controller\CurrencyActions;

use App\Presenters\CurrencyPresenter;
use App\Services\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class UpdateCurrencyAction extends AbstractController
{
    public function __construct(
        private readonly CurrencyService $currencyService,
        private readonly CurrencyPresenter $currencyPresenter
    ) {}
    #[Route("api/currencies/{id}", name: "api_currency_update", methods: ["PUT"])]
    #[OA\Put(
        description: "Modifies the name of an existing currency.",
        summary: "Update an existing currency",
        requestBody: new OA\RequestBody(
            description: "New currency name",
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", description: "The updated currency name", type: "string", example: "United States Dollar Updated")
                ],
                type: "object"
            )
        ),
        tags: ["Currency"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The code of the currency to update',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'USD')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Currency updated successfully",
                content: new OA\JsonContent( // Припускаємо, що повертається оновлений об'єкт
                    properties: [
                        new OA\Property(property: "id", type: "string", example: "USD"),
                        new OA\Property(property: "name", type: "string", example: "United States Dollar Updated")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid JSON payload or missing 'name' field"),
            new OA\Response(response: 404, description: "Not Found - Currency with the specified ID not found"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function updateCurrency(Request $request, string $id): JsonResponse
    {
        $data = json_decode($request->getContent());
        if($data === []) {
            return new JsonResponse(null,Response::HTTP_BAD_REQUEST);
        }
        $name = $data->name;
        return new JsonResponse(
            $this->currencyPresenter->present($this->currencyService->updateCurrency($id, $name)),
            Response::HTTP_OK,
            []
        );
    }
}
