<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\PaginatedListDTO;
use App\DTO\RegisterDto;
use App\DTO\UpdateProfileDto;
use App\Presenters\UserPresenter;
use App\Services\UserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UserPresenter $userPresenter,
    )
    {}

    #[Route("/api/login", name: "api_login", methods: ["POST"])]
    #[OA\Post(
        path: "/api/login",
        description: "JWT Authentication",
        summary: "Login for user",
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["username", "password"],
                properties: [
                    new OA\Property(property: "username", type: "string", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password")
                ]
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Wrong login or password"
            )
        ]
    )]
    public function fakeLogin(): JsonResponse
    {
        return new JsonResponse(['message'=>'just for swagger']);
    }

    #[Route("/api/register", name: "api_register", methods: ["POST"])]
    #[OA\Post(
        description: "Creates a new user account based on provided data.",
        summary: "Register a new user",
        security: [],
        requestBody: new OA\RequestBody(
            description: "Data for the new user",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: RegisterDto::class))
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 201,
                description: "User successfully registered",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", format:"uuid", example: "a1b2c3d4-e5f6-7890-1234-567890abcdef"),
                        new OA\Property(property: "role", type: "string", enum: ["ROLE_ADMIN", "ROLE_AGENT", "ROLE_CUSTOMER"], example: "ROLE_CUSTOMER"),
                        new OA\Property(property: "email", type: "string", format:"email", example: "newuser@example.com"),
                        new OA\Property(property: "name", type: "string", example: "John Doe"),
                        new OA\Property(property: "phone", type: "string", example: "+380991234567"),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation failed (e.g., invalid email format, missing fields, password too short)"
            ),
            new OA\Response(
                response: 409,
                description: "User with this email already exists"
            )
        ]
    )]
    public function register(
        #[MapRequestPayload] RegisterDto $registerDto,
    ): JsonResponse
    {
        $newUser = $this->userService->register($registerDto);
        if(!$newUser->getEmail())
        {
            return new JsonResponse(['message'=>'user already exists'], Response::HTTP_CONFLICT, []);
        }
        return new JsonResponse($this->userPresenter->present($newUser), Response::HTTP_CREATED, []);
    }

    #[Route('api/block', name: "api_block", methods: ["POST"])]
//    #[Security(name: 'Bearer')]
    #[OA\Post(
        description: "Marks a user account as blocked.",
        summary: "Block a user",
        requestBody: new OA\RequestBody(
            description: "ID of the user to block",
            required: true,
            content: new OA\JsonContent(
                required: ["id"],
                properties: [
                    new OA\Property(property: "id", description: "User's unique identifier", type: "string", format:"uuid", example: "a1b2c3d4-e5f6-7890-1234-567890abcdef")
                ]
            )
        ),
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 204,
                description: "User successfully blocked"
            ),
            new OA\Response(
                response: 400,
                description: "Invalid JSON payload or missing 'id' field"
            ),
            new OA\Response(
                response: 404,
                description: "User with the specified ID not found"
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User does not have permission to block users"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - JWT token missing or invalid"
            )
        ]
    )]
    public function block(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return new JsonResponse(["message" => "Некоректний JSON", "data"=>$data], Response::HTTP_BAD_REQUEST);
        }
        $id = $data["id"];
        $this->userService->blockUser($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route("/api/unblock", name: "api_unblock", methods: ["POST"])]
//    #[Security(name: 'Bearer')]
    #[OA\Post(
        description: "Removes the blocked status from a user account.",
        summary: "Unblock a user",
        requestBody: new OA\RequestBody(
            description: "ID of the user to unblock",
            required: true,
            content: new OA\JsonContent(
                required: ["id"],
                properties: [
                    new OA\Property(property: "id", description: "User's unique identifier", type: "string", format:"uuid", example: "a1b2c3d4-e5f6-7890-1234-567890abcdef")
                ]
            )
        ),
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 204, // HTTP_NO_CONTENT
                description: "User successfully unblocked"
            ),
            new OA\Response(
                response: 400,
                description: "Invalid JSON payload or missing 'id' field"
            ),
            new OA\Response(
                response: 404,
                description: "User with the specified ID not found"
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden - User does not have permission to unblock users"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - JWT token missing or invalid"
            )
        ]
    )]
    public function unBlock(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return new JsonResponse(["message" => "Некоректний JSON", "data"=>$data], Response::HTTP_BAD_REQUEST);
        }
        $id = $data["id"];
        $this->userService->unblockUser($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route("/api/users", name: "api_get_users", methods: ["POST"])]
//    #[Security(name: 'Bearer')]
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

    #[Route("/api/user/{id}", name: "api_update_user", methods: ["PUT"])]
//    #[Security(name: 'Bearer')]
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
