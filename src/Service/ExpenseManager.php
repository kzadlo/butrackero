<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Hydrator\BalanceHydrator;
use App\Hydrator\ExpenseHydratorStrategy;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseManager
{
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

    public function getExpenseAsArray(int $id): array
    {
        return $this->hydrator->extract(
            $this->entityManager->find(Expense::class, $id),
            $this->hydrationStrategy
        );
    }

    public function addExpense(array $expenseValues): void
    {
        $expense = $this->hydrator->hydrate($expenseValues, $this->hydrationStrategy, $this->entityManager);

        $this->entityManager->persist($expense);
        $this->entityManager->flush();
    }

    public function deleteExpense(int $id): void
    {
        $this->entityManager->remove($this->entityManager->find(Expense::class, $id));
        $this->entityManager->flush();
    }

    public function updateExpense(int $id, array $updateValues): void
    {
        $expense = $this->entityManager->find(Expense::class, $id);

        if (!empty($updateValues['amount'])) {
            $expense->setAmount($updateValues['amount']);
        }

        if (!empty($updateValues['category'])) {
            $category = $this->entityManager->find(ExpenseCategory::class, $updateValues['category']);

            $expense->setCategory($category);
        }

        $this->entityManager->flush();
    }

    public function getAllExpensesAsArray(): array
    {
        $expenses = $this->entityManager->getRepository(Expense::class)->findAll();

        return $this->hydrator->extractSeveral($expenses, $this->hydrationStrategy);
    }
}