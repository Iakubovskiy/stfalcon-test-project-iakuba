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

class CreateCurrencyAction extends AbstractController
{
    public function __construct(
        private readonly CurrencyService $currencyService,
        private readonly CurrencyPresenter $currencyPresenter
    ) {}

    #[Route("api/currencies", name: "api_currency_create", methods: ["POST"])]
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
}
