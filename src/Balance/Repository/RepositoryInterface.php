<?php

namespace App\Balance\Repository;

use App\Balance\Model\BalanceEntityInterface;

interface RepositoryInterface
{
    public function save(BalanceEntityInterface $entity): void;

    public function delete(BalanceEntityInterface $entity): void;
}
