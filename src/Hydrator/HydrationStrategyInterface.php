<?php

namespace App\Hydrator;

use App\Entity\BalanceEntityInterface;
use Doctrine\ORM\EntityManagerInterface;

interface HydrationStrategyInterface
{
    public function extract(BalanceEntityInterface $entity): array;
    public function hydrate(array $data, EntityManagerInterface $entityManager): BalanceEntityInterface;

}