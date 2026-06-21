<?php

/**
 * Category service.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    /**
     * Constructor.
     *
     * @param CategoryRepository $categoryRepository Category repository
     * @param PaginatorInterface $paginator          Paginator
     * @param ArticleRepository  $articleRepository  Article repository
     */
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly PaginatorInterface $paginator, private readonly ArticleRepository $articleRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<int, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => [
                    'category.id',
                    'category.name',
                ],
                'defaultSortFieldName' => 'category.name',
                'defaultSortDirection' => 'asc',
            ]
        );
    }

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Can Category be deleted?
     *
     * @param Category $category Category entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Category $category): bool
    {
        try {
            $result = $this->articleRepository->countByCategory($category);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
