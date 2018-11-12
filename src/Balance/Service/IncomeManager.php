<?php

namespace App\Balance\Service;

use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\IncomeHydratorStrategy;
use Doctrine\ORM\EntityManagerInterface;

class IncomeManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $entityManager;

    public function __construct(
        BalanceHydrator $hydrator,
        IncomeHydratorStrategy $strategy,
        EntityManagerInterface $entityManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->entityManager = $entityManager;
    }

    public function getIncomeAsArray(int $id): array
    {
        return $this->hydrator->extract(
            $this->entityManager->find(Income::class, $id),
            $this->hydrationStrategy
        );
    }

    public function addIncome(array $expenseValues): void
    {
        $income = $this->hydrator->hydrate($expenseValues, $this->hydrationStrategy, $this->entityManager);

        $this->entityManager->persist($income);
        $this->entityManager->flush();
    }

    public function deleteIncome(int $id): void
    {
        $this->entityManager->remove($this->entityManager->find(Income::class, $id));
        $this->entityManager->flush();
    }

    public function updateIncome(int $id, array $updateValues): void
    {
        $income = $this->entityManager->find(Income::class, $id);

        if (!empty($updateValues['amount'])) {
            $income->setAmount($updateValues['amount']);
        }

        if (!empty($updateValues['type'])) {
            $type = $this->entityManager->find(IncomeType::class, $updateValues['type']);

            $income->setType($type);
        }

        $this->entityManager->flush();
    }

    public function getAllIncomesAsArray(): array
    {
        $expenses = $this->entityManager->getRepository(Income::class)->findAll();

        return $this->hydrator->extractSeveral($expenses, $this->hydrationStrategy);
    }
}