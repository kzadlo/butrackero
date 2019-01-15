<?php

namespace App\Balance\Service;

use App\Application\Service\UserManager;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\CategoryHydratorStrategy;
use App\Balance\Model\ExpenseCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $entityManager;

    private $userManager;

    public function __construct(
        BalanceHydrator $hydrator,
        CategoryHydratorStrategy $strategy,
        EntityManagerInterface $entityManager,
        UserManager $userManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->entityManager = $entityManager;
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

    public function save(ExpenseCategory $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function delete(ExpenseCategory $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    public function update(ExpenseCategory $category, array $updateValues): void
    {
        if (isset($updateValues['name'])) {
            $category->setName($updateValues['name']);
        }

        if (isset($updateValues['description'])) {
            $category->setDescription($updateValues['description']);
        }

        $this->save($category);
    }

    public function getFiltered(array $params): array
    {
        $author = $this->getCategoryAuthor();

        if (!$author) {
            return [];
        }

        $categories = $this->entityManager->getRepository(ExpenseCategory::class)->findByAuthorAndFilters($author->getId(), $params);

        return $this->hydrator->extractSeveral($categories, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        $author = $this->getCategoryAuthor();

        if (!$author) {
            return 0;
        }

        return $this->entityManager->getRepository(ExpenseCategory::class)->findByAuthorAndFilters($author->getId(), $params, true);
    }

    public function getCategoryAuthor(): ?UserInterface
    {
        return $this->userManager->getCurrentUser();
    }
}