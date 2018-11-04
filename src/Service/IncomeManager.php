<?php

namespace App\Service;

use App\Entity\Income;
use App\Entity\IncomeType;
use Doctrine\ORM\EntityManagerInterface;

class IncomeManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateIncome(Income $income, array $updateValues): void
    {
        if (isset($updateValues['amount'])) {
            $income->setAmount($updateValues['amount']);
        }

        if (isset($updateValues['type'])) {
            $type = $this->entityManager->find(IncomeType::class, $updateValues['type']);

            $income->setType($type);
        }

        $this->entityManager->flush();
    }

    public function extractOneIncome(Income $income): array
    {
        $extractedIncome['id'] = $income->getId();
        $extractedIncome['amount'] = $income->getAmount();
        $extractedIncome['type'] = $income->getType()->getName();
        $extractedIncome['created_timestamp'] = $income->getCreated()->getTimestamp();

        return $extractedIncome;
    }

    public function prepareIncomes(array $incomes): array
    {
        $data = [];
        foreach ($incomes as $income) {
            $data[] = $this->extractOneIncome($income);
        }

        return $data;
    }
}