<?php

namespace App\Balance\Service;

use App\Application\Service\UserManager;
use App\Balance\Model\Expense;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\ExpenseHydratorStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ExpenseManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $entityManager;

    private $userManager;

    public function __construct(
        BalanceHydrator $hydrator,
        ExpenseHydratorStrategy $strategy,
        EntityManagerInterface $entityManager,
        UserManager $userManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
    }

    public function getAsArray(Expense $expense): array
    {
        return $this->hydrator->extract($expense, $this->hydrationStrategy);
    }

    public function createFromArray(array $expenseValues): Expense
    {
        return $this->hydrator->hydrate($expenseValues, $this->hydrationStrategy);
    }

    public function save(Expense $expense): void
    {
        $this->entityManager->persist($expense);
        $this->entityManager->flush();
    }

    public function delete(Expense $expense): void
    {
        $this->entityManager->remove($expense);
        $this->entityManager->flush();
    }

    public function update(Expense $expense, array $updateValues): void
    {
        if (isset($updateValues['amount'])) {
            $expense->setAmount($updateValues['amount']);
        }

        if (isset($updateValues['category'])) {
            $expense->setCategory($updateValues['category']);
        }

        $this->save($expense);
    }

    public function getFiltered(array $params): array
    {
        $author = $this->getExpenseAuthor();

        if (!$author) {
            return [];
        }

        $expenses = $this->entityManager->getRepository(Expense::class)->findByAuthorAndFilters($author->getId(), $params);

        return $this->hydrator->extractSeveral($expenses, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        $author = $this->getExpenseAuthor();

        if (!$author) {
            return 0;
        }

        return $this->entityManager->getRepository(Expense::class)->findByAuthorAndFilters($author->getId(), $params, true);
    }

    public function getExpenseAuthor(): ?UserInterface
    {
        return $this->userManager->getCurrentUser();
    }
}