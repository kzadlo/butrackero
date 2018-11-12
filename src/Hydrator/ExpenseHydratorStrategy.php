<?php

namespace App\Hydrator;

use App\Entity\BalanceEntityInterface;
use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseHydratorStrategy extends HydrationStrategy
{
    /** @var Expense $entity */
    public function extract(BalanceEntityInterface $entity): array
    {
        return [
            'id' => $entity->getId(),
            'amount' => $entity->getAmount(),
            'category' => $entity->getCategory()->getName(),
            'created_timestamp' => $entity->getCreated()->getTimestamp()
        ];
    }

    public function hydrate(array $data, EntityManagerInterface $entityManager): BalanceEntityInterface
    {
        return (new Expense())
            ->setAmount($data['amount'])
            ->setCategory($entityManager->find(ExpenseCategory::class, $data['category']));
    }
}