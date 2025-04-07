<?php
declare(strict_types=1);


namespace App\Controller\CurrencyActions;

use App\Presenters\CurrencyPresenter;
use App\Services\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class GetCurrenciesAction extends AbstractController
{
    public function __construct(
        private readonly CurrencyService $currencyService,
        private readonly CurrencyPresenter $currencyPresenter
    ) {}

    #[Route("/api/currencies", name: "api_currencies", methods: ["GET"])]
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
}
