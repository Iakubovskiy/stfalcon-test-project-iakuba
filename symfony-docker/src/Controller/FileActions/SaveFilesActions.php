<?php
declare(strict_types=1);


namespace App\Controller\FileActions;

use App\Presenters\FilePresenter;
use App\Services\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class SaveFilesActions extends AbstractController
{
    public function __construct(
        private readonly FileService $fileService,
        private readonly FilePresenter $filePresenter)
    {}
    #[Route('api/files', name: 'api_files_save', methods: ['POST'])]
    #[OA\Post(
        operationId: 'uploadFiles',
        description: 'Uploads multiple image files and returns their metadata.',
        summary: 'Upload multiple files',
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['images[]'],
                        properties: [
                            new OA\Property(
                                property: 'images[]',
                                description: 'Array of image files',
                                type: 'array',
                                items: new OA\Items(type: 'string', format: 'binary')
                            )
                        ],
                        type: 'object'
                    )
                )
            ]
        ),
        tags: ['Files'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of successfully uploaded files',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid', example: 'e1b4c2ae-58b9-4a09-8c5b-6f3d847917b0'),
                            new OA\Property(property: 'url', type: 'string', format: 'uri', example: 'https://example.com/uploads/example.jpg')
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 400, description: 'Bad Request - No files or invalid format'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden')
        ]
    )]

    public function saveFiles(Request $request):JsonResponse
    {
        $images = $request->files->get('images');
        $savedFiles = $this->fileService->saveFilesToDb($images);
        return new JsonResponse(
            $this->filePresenter->presentArray($savedFiles),
            Response::HTTP_OK,
            []
        );
    }
}
