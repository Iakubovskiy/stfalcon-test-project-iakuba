<?php
declare(strict_types=1);


namespace App\Controller\UserActions;

use App\DTO\UpdateProfileDto;
use App\Presenters\UserPresenter;
use App\Services\UserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class UpdateUserAction extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPresenter $userPresenter,
    )
    {}

    #[Route("/api/users/{id}", name: "api_update_user", methods: ["PUT"])]
    #[OA\Put(
        description: "Updates specified user's profile information.",
        summary: "Update user profile",
        requestBody: new OA\RequestBody(
            description: "Updated user data. **Note:** Using RegisterDto structure.",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: UpdateProfileDto::class))
        ),
        tags: ["Users"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The UUID of the user to update',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User profile updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", format:"uuid"),
                        new OA\Property(property: "role", type: "string", enum: ["ROLE_ADMIN", "ROLE_AGENT", "ROLE_CUSTOMER"]),
                        new OA\Property(property: "email", type: "string", format:"email"),
                        new OA\Property(property: "name", type: "string"),
                        new OA\Property(property: "phone", type: "string"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400, // Або 422
                description: "Validation failed for the provided data"
            ),
            new OA\Response(
                response: 404,
                description: "User with the specified ID not found"
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User does not have permission to update this profile (e.g., updating another user)"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - JWT token missing or invalid"
            ),
            new OA\Response(
                response: 409,
                description: "Email already exists (if email was changed and is not unique)"
            )
        ]
    )]
    public function updateUser(
        #[MapRequestPayload]UpdateProfileDto $newUser,
        Uuid $id,
    ): JsonResponse
    {
        return new JsonResponse(
            $this->userPresenter->present($this->userService->updateProfile($id, $newUser)),
            Response::HTTP_OK,
            []
        );
    }
}
