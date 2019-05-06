<?php

namespace App\Balance\Service;

use App\Application\Model\User;
use App\Application\Service\UserManager;
use App\Balance\Model\Expense;
use App\Balance\Hydrator\BalanceHydrator;
use App\Balance\Hydrator\ExpenseHydratorStrategy;
use App\Balance\Repository\ExpenseRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class ExpenseManager
{
    private $hydrator;

    private $hydrationStrategy;

    private $expenseRepository;

    private $userManager;

    public function __construct(
        BalanceHydrator $hydrator,
        ExpenseHydratorStrategy $strategy,
        ExpenseRepository $expenseRepository,
        UserManager $userManager
    ) {
        $this->hydrator = $hydrator;
        $this->hydrationStrategy = $strategy;
        $this->expenseRepository = $expenseRepository;
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

    public function update(Expense $expense, array $updateValues): void
    {
        if (isset($updateValues['amount'])) {
            $expense->setAmount($updateValues['amount']);
        }

        if (isset($updateValues['category'])) {
            $expense->setCategory($updateValues['category']);
        }

        $this->expenseRepository->save($expense);
    }

    public function getFiltered(array $params): array
    {
        /** @var User $author */
        $author = $this->getExpenseAuthor();

        if (!$author) {
            return [];
        }

        $expenses = $this->expenseRepository->findByAuthorAndFilters($author->getId(), $params);

        return $this->hydrator->extractSeveral($expenses, $this->hydrationStrategy);
    }

    public function countFiltered(array $params): int
    {
        /** @var User $author */
        $author = $this->getExpenseAuthor();

        if (!$author) {
            return 0;
        }

        return $this->expenseRepository->findByAuthorAndFilters($author->getId(), $params, true);
    }

    public function getExpenseAuthor(): ?UserInterface
    {
        return $this->userManager->getCurrentUser();
    }
}
