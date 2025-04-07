<?php
declare(strict_types=1);


namespace App\Controller\CurrencyActions;

use App\Services\CurrencyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class DeleteCurrencyAction extends AbstractController
{
    public function __construct(
        private readonly CurrencyService $currencyService,
    ) {}

    #[Route("api/currencies/{id}", name: "api_currency_delete", methods: ["DELETE"])]
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
}
