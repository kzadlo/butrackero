<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateExpense(Expense $expense, array $updateValues): void
    {
        if (isset($updateValues['amount'])) {
            $expense->setAmount($updateValues['amount']);
        }

        if (isset($updateValues['category'])) {
            $category = $this->entityManager->find(ExpenseCategory::class, $updateValues['category']);

            $expense->setCategory($category);
        }

        $this->entityManager->flush();
    }

    public function extractOneExpense(Expense $expense): array
    {
        $extractedExpense['id'] = $expense->getId();
        $extractedExpense['amount'] = $expense->getAmount();
        $extractedExpense['category'] = $expense->getCategory()->getName();
        $extractedExpense['created_timestamp'] = $expense->getCreated()->getTimestamp();

        return $extractedExpense;
    }

    public function prepareExpenses(array $expenses): array
    {
        $data = [];
        foreach ($expenses as $expense) {
            $data[] = $this->extractOneExpense($expense);
        }

        return $data;
    }
}