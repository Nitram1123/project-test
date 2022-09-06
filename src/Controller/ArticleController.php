<?php

declare(strict_types=1);

namespace App\Controller;

use App\Processor\ProcessorInterface;
use App\Provider\ProviderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    public function __construct(
        private ProcessorInterface $articleCrudProcessor,
        private ProviderInterface $articleCrudProvider,
    ) {
    }

    #[Route('api/articles', name: 'app_article_search', methods:['GET'])]
    public function search(Request $request): JsonResponse
    {
        $paginatorDTO = $this->articleCrudProvider->provide($request->query->all(), ProviderInterface::SEARCH);

        return $this->json($paginatorDTO, 200, [], [
            'groups' => [ 'paginator', 'item:read', 'article:read' ],
        ]);
    }

    #[Route('api/articles/{id}', name: 'app_article_read', methods:['GET'])]
    public function read(string $id): JsonResponse
    {
        $article = $this->articleCrudProvider->provide(null, ProviderInterface::READ, $id);

        return $this->json($article, 200, [], [
            'groups' => [ 'item:read', 'article:read' ],
        ]);
    }

    /** @IsGranted("ROLE_ADMIN") */
    #[Route('api/articles', name: 'app_article_create', methods:['POST'])]
    public function create(Request $request): JsonResponse
    {
        $article = $this->articleCrudProcessor->process($request->getContent(), ProcessorInterface::CREATE);

        return $this->json($article, 201, [], [
            'groups' => [ 'item:read', 'article:read' ],
        ]);
    }

    /** @IsGranted("ROLE_ADMIN") */
    #[Route('api/articles/{id}', name: 'app_article_update', methods:['PUT'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $article = $this->articleCrudProcessor->process($request->getContent(), ProcessorInterface::UPDATE, $id);

        return $this->json($article, 200, [], [
            'groups' => [ 'item:read', 'article:read' ],
        ]);
    }

    /** @IsGranted("ROLE_ADMIN") */
    #[Route('api/articles/{id}', name: 'app_article_delete', methods:['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $this->articleCrudProcessor->process(null, ProcessorInterface::DELETE, $id);

        return $this->json([], 204);
    }
}
