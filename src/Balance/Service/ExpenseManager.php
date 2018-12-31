<?php

namespace App\Balance\Service;

use App\Balance\Model\Expense;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\ExpenseHydratorStrategy;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseManager
{
    // Test author ID - added for creating new features - should be deleted when authentication will be implemented
    CONST TEST_ID = 1;

    private $hydrator;

    private $hydrationStrategy;

    private $entityManager;

    public function __construct(
        BalanceHydrator $hydrator,
        ExpenseHydratorStrategy $strategy,
        EntityManagerInterface $entityManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->entityManager = $entityManager;
    }

    public function getExpenseAsArray(Expense $expense): array
    {
        return $this->hydrator->extract($expense, $this->hydrationStrategy);
    }

    public function createExpenseFromArray(array $expenseValues): Expense
    {
        return $this->hydrator->hydrate($expenseValues, $this->hydrationStrategy);
    }

    public function addExpense(Expense $expense): void
    {
        $this->entityManager->persist($expense);
        $this->entityManager->flush();
    }

    public function deleteExpense(Expense $expense): void
    {
        $this->entityManager->remove($expense);
        $this->entityManager->flush();
    }

    public function updateExpense(Expense $expense, array $updateValues): void
    {
        if (isset($updateValues['amount'])) {
            $expense->setAmount($updateValues['amount']);
        }

        if (isset($updateValues['category'])) {
            $expense->setCategory($updateValues['category']);
        }

        $this->entityManager->flush();
    }

    public function getFilteredExpenses(array $params): array
    {
        $expenses = $this->entityManager->getRepository(Expense::class)->findByAuthorAndFilters(self::TEST_ID, $params);

        return $this->hydrator->extractSeveral($expenses, $this->hydrationStrategy);
    }

    public function countFilteredExpenses(array $params): int
    {
        return $this->entityManager->getRepository(Expense::class)->findByAuthorAndFilters(self::TEST_ID, $params, true);
    }
}