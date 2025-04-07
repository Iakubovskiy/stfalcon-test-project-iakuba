<?php
declare(strict_types=1);


namespace App\Controller\CurrencyActions;

use App\Services\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class ConvertCurrencyAction extends AbstractController
{
    public function __construct(
        private readonly CurrencyService $currencyService,
    ) {}

    #[Route("api/currencies/convert", name: "api_currency_convert", methods: ["POST"])]
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
