<?php

namespace App\Balance\Service;

use App\Application\Model\User;
use App\Application\Service\UserManager;
use App\Balance\Model\Income;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\IncomeHydratorStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IncomeManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $entityManager;

    private $userManager;

    public function __construct(
        BalanceHydrator $hydrator,
        IncomeHydratorStrategy $strategy,
        EntityManagerInterface $entityManager,
        UserManager $userManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
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

        $this->save($income);
    }

    public function getFiltered(array $params): array
    {
        /** @var User $author */
        $author = $this->getIncomeAuthor();

        if (!$author) {
            return [];
        }

        $incomes = $this->entityManager->getRepository(Income::class)
            ->findByAuthorAndFilters($author->getId(), $params);

        return $this->hydrator->extractSeveral($incomes, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        /** @var User $author */
        $author = $this->getIncomeAuthor();

        if (!$author) {
            return 0;
        }

        return $this->entityManager->getRepository(Income::class)
            ->findByAuthorAndFilters($author->getId(), $params, true);
    }

    public function getIncomeAuthor(): ?UserInterface
    {
        return $this->userManager->getCurrentUser();
    }
}