<?php

namespace App\Balance\Service;

use App\Application\Model\User;
use App\Application\Service\UserManager;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\CategoryHydratingStrategy;
use App\Balance\Model\ExpenseCategory;
use App\Balance\Repository\ExpenseCategoryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $categoryRepository;

    private $userManager;

    public function __construct(
        BalanceHydrator $hydrator,
        CategoryHydratingStrategy $strategy,
        ExpenseCategoryRepository $categoryRepository,
        UserManager $userManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->categoryRepository = $categoryRepository;
        $this->userManager = $userManager;
    }

    public function getAsArray(ExpenseCategory $category): array
    {
        return $this->hydrator->extract($category, $this->hydrationStrategy);
    }

    public function createFromArray(array $categoryValues): ExpenseCategory
    {
        return $this->hydrator->hydrate($categoryValues, $this->hydrationStrategy);
    }

    public function update(ExpenseCategory $category, array $updateValues): void
    {
        if (isset($updateValues['name'])) {
            $category->changeName($updateValues['name']);
        }

        if (isset($updateValues['description'])) {
            $category->changeDescription($updateValues['description']);
        }

        $this->categoryRepository->save($category);
    }

    public function getFiltered(array $params): array
    {
        /** @var User $author */
        $author = $this->getCategoryAuthor();

        if (!$author) {
            return [];
        }

        $categories = $this->categoryRepository->findByAuthorAndFilters($author->getId(), $params);

        return $this->hydrator->extractSeveral($categories, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        /** @var User $author */
        $author = $this->getCategoryAuthor();

        if (!$author) {
            return 0;
        }

        return $this->categoryRepository->findByAuthorAndFilters($author->getId(), $params, true);
    }

    public function getCategoryAuthor(): ?UserInterface
    {
        return $this->userManager->getCurrentUser();
    }
}
