<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ArticleService implements ArticleServiceInterface
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly PaginatorInterface $paginator,
    ) {
    }

    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->queryAll(),
            $page,
            ArticleRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => [
                    'article.id',
                    'article.createdAt',
                    'article.title',
                    'article.content',
                    'category.name',
                ],
                'defaultSortFieldName' => 'article.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    public function getPaginatedListByCategory(int $page, Category $category): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->queryByCategory($category),
            $page,
            ArticleRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => [
                    'article.id',
                    'article.createdAt',
                    'article.title',
                    'article.content',
                ],
                'defaultSortFieldName' => 'article.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    public function save(Article $article): void
    {
        if (null === $article->getId()) {
            $article->setCreatedAt(
                new \DateTimeImmutable()
            );
        }
        $this->articleRepository->save($article);
    }

    public function delete(Article $article): void
    {
        $this->articleRepository->delete($article);
    }
}
