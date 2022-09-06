<?php

declare(strict_types=1);

namespace App\Controller;

use App\Processor\ProcessorInterface;
use App\Provider\ProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(
        private ProcessorInterface $commentCrudProcessor,
        private ProviderInterface $commentCrudProvider,
    ) {
    }

    #[Route('api/comments', name: 'app_comment_search', methods:['GET'])]
    public function search(Request $request): JsonResponse
    {
        $paginatorDTO = $this->commentCrudProvider->provide($request->query->all(), ProviderInterface::SEARCH);

        return $this->json($paginatorDTO, 200, [], [
            'groups' => [ 'paginator', 'item:read', 'comment:read' ],
        ]);
    }

    #[Route('api/comments/{id}', name: 'app_comment_read', methods:['GET'])]
    public function read(string $id): JsonResponse
    {
        $comment = $this->commentCrudProvider->provide(null, ProviderInterface::READ, $id);

        return $this->json($comment, 200, [], [
            'groups' => [ 'item:read', 'comment:read' ],
        ]);
    }

    #[Route('api/comments', name: 'app_comment_create', methods:['POST'])]
    public function create(Request $request): JsonResponse
    {
        $comment = $this->commentCrudProcessor->process($request->getContent(), ProcessorInterface::CREATE);

        return $this->json($comment, 201, [], [
            'groups' => [ 'item:read', 'comment:read' ],
        ]);
    }

    #[Route('api/comments/{id}', name: 'app_comment_update', methods:['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $comment = $this->commentCrudProcessor->process($request->getContent(), ProcessorInterface::UPDATE, $id);

        return $this->json($comment, 200, [], [
            'groups' => [ 'item:read', 'comment:read' ],
        ]);
    }

    #[Route('api/comments/{id}', name: 'app_comment_delete', methods:['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $this->commentCrudProcessor->process(null, ProcessorInterface::DELETE, $id);

        return $this->json([], 204);
    }
}
