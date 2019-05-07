<?php

namespace App\Balance\Model;

use Ramsey\Uuid\UuidInterface;

interface BalanceEntityInterface
{
    public function getId(): UuidInterface;
}
