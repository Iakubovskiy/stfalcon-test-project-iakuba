<?php
declare(strict_types=1);

namespace App\Controller;

use App\Presenters\CurrencyPresenter;
use App\Services\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class CurrencyController extends AbstractController
{
    public function __construct(
        private readonly CurrencyService $currencyService,
        private readonly CurrencyPresenter $currencyPresenter
    ) {}

    #[Route("/api/currency", name: "api_currency", methods: ["GET"])]
    #[OA\Get(
        description: "Retrieves all available currencies.",
        summary: "Get list of currencies",
        tags: ["Currency"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of currencies",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", description: "Currency code (e.g., USD, EUR)", type: "string", example: "USD"),
                            new OA\Property(property: "name", description: "Currency name", type: "string", example: "US Dollar")
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function getCurrency(): JsonResponse
    {
        $currencies = $this->currencyService->getCurrencies();

        return new JsonResponse($this->currencyPresenter->presentList($currencies), Response::HTTP_OK, [],);
    }

    #[Route("api/currency/create", name: "api_currency_create", methods: ["POST"])]
    #[OA\Post(
        description: "Adds a new currency definition.",
        summary: "Create a new currency",
        requestBody: new OA\RequestBody(
            description: "Currency data",
            required: true,
            content: new OA\JsonContent(
                required: ["id", "name"],
                properties: [
                    new OA\Property(property: "id", description: "Unique currency code (e.g., UAH)", type: "string", example: "UAH"),
                    new OA\Property(property: "name", description: "Currency name", type: "string", example: "Ukrainian Hryvnia")
                ],
                type: "object"
            )
        ),
        tags: ["Currency"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Currency created successfully",
                content: new OA\JsonContent( // Припускаємо, що повертається створений об'єкт
                    properties: [
                        new OA\Property(property: "id", type: "string", example: "UAH"),
                        new OA\Property(property: "name", type: "string", example: "Ukrainian Hryvnia")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid JSON payload or missing fields"),
            new OA\Response(response: 409, description: "Conflict - Currency with this ID already exists"), // Припущення
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function createCurrency(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if($data===[]){
            return new JsonResponse(null,Response::HTTP_BAD_REQUEST);
        }
        $id = $data["id"];
        $name = $data["name"];
        return new JsonResponse(
            $this->currencyPresenter->present($this->currencyService->createCurrency($id, $name)),
            Response::HTTP_CREATED,
            [],
        );
    }

    #[Route("api/currency/{id}", name: "api_currency_update", methods: ["PUT"])]
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

    #[Route("api/currency/{id}/delete", name: "api_currency_delete", methods: ["DELETE"])]
    #[OA\Delete(
        description: "Removes a currency definition.",
        summary: "Delete a currency",
        tags: ["Currency"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The code of the currency to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'JPY')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Currency deleted successfully"
            ),
            new OA\Response(response: 404, description: "Not Found - Currency with the specified ID not found"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function deleteCurrency(string $id): JsonResponse
    {
       $this->currencyService->deleteCurrency($id);
        return new JsonResponse(
            Response::HTTP_NO_CONTENT,
        );
    }

    #[Route("api/currency/convert", name: "api_currency_convert", methods: ["POST"])]
    #[OA\Post(
        description: "Calculates the equivalent amount in a target currency.",
        summary: "Convert an amount between currencies",
        requestBody: new OA\RequestBody(
            description: "Conversion details",
            required: true,
            content: new OA\JsonContent(
                required: ["from", "to", "amount"],
                properties: [
                    new OA\Property(property: "from", description: "Source currency code", type: "string", example: "USD"),
                    new OA\Property(property: "to", description: "Target currency code", type: "string", example: "EUR"),
                    new OA\Property(property: "amount", description: "Amount in source currency", type: "number", format: "float", example: 100.50)
                ],
                type: "object"
            )
        ),
        tags: ["Currency"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Conversion successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "fromCurrency", type: "string", example: "USD"),
                        new OA\Property(property: "toCurrency", type: "string", example: "EUR"),
                        new OA\Property(property: "originalAmount", type: "number", format: "float", example: 100.50),
                        new OA\Property(property: "convertedAmount", type: "number", format: "float", example: 95.48),
                        new OA\Property(property: "rate", type: "number", format: "float", example: 0.95)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid JSON, missing fields, invalid amount, or unknown currency codes"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function convertCurrency(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if($data === []) {
            return new JsonResponse(null,Response::HTTP_BAD_REQUEST);
        }
        $currencyFrom = $data["from"];
        $currencyTo = $data["to"];
        $amount = $data["amount"];

        return new JsonResponse(
            ['convertedValue' => $this->currencyService->convertCurrency($currencyFrom, $currencyTo, $amount)],
            Response::HTTP_OK,
            [],
        );
    }
}
