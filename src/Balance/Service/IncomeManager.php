<?php

namespace App\Balance\Service;

use App\Balance\Model\Income;
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

    public function getAsArray(Income $income): array
    {
        return $this->hydrator->extract($income, $this->hydrationStrategy);
    }

    public function createFromArray(array $incomeValues): Income
    {
        return $this->hydrator->hydrate($incomeValues, $this->hydrationStrategy);
    }

    public function save(Income $income): void
    {
        $this->entityManager->persist($income);
        $this->entityManager->flush();
    }

    public function delete(Income $income): void
    {
        $this->entityManager->remove($income);
        $this->entityManager->flush();
    }

    public function update(Income $income, array $updateValues): void
    {
        if (isset($updateValues['amount'])) {
            $income->setAmount($updateValues['amount']);
        }

        if (isset($updateValues['type'])) {
            $income->setType($updateValues['type']);
        }

        $this->entityManager->flush();
    }

    public function getFiltered(array $params): array
    {
        $incomes = $this->entityManager->getRepository(Income::class)->findByAuthorAndFilters(ExpenseManager::TEST_ID, $params);

        return $this->hydrator->extractSeveral($incomes, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        return $this->entityManager->getRepository(Income::class)->findByAuthorAndFilters(ExpenseManager::TEST_ID, $params, true);
    }
}