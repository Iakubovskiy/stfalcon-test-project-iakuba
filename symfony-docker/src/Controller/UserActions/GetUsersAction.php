<?php
declare(strict_types=1);


namespace App\Controller\UserActions;

use App\DTO\PaginatedListDTO;
use App\Presenters\UserPresenter;
use App\Services\UserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class GetUsersAction extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPresenter $userPresenter,
    )
    {}

    #[Route("/api/admin/users", name: "api_get_users", methods: ["POST"])]
    #[OA\Post(
        description: "Retrieves a list of all registered users.",
        summary: "Get list of users",
        requestBody: new OA\RequestBody(
            description: "Data for the new user",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PaginatedListDTO::class))
        ),
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "string", format:"uuid", example: "a1b2c3d4-e5f6-7890-1234-567890abcdef"),
                            new OA\Property(property: "role", type: "string", enum: ["ROLE_ADMIN", "ROLE_AGENT", "ROLE_CUSTOMER"], example: "ROLE_CUSTOMER"),
                            new OA\Property(property: "email", type: "string", format:"email", example: "user@example.com"),
                            new OA\Property(property: "name", type: "string", example: "John Doe"),
                            new OA\Property(property: "phone", type: "string", example: "+380991234567"),
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User does not have permission to view user list"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - JWT token missing or invalid"
            )
        ]
    )]
    public function getUsers(
        #[MapRequestPayload] PaginatedListDTO $metadata
    ): JsonResponse
    {
        return new JsonResponse(
            $this->userPresenter->presentPaginatedList($this->userService->getAllUsers(
                $metadata->offset,
                $metadata->limit,
            )),
            Response::HTTP_OK,
            [],
        );
    }
}
