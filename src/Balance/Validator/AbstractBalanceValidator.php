<?php

namespace App\Balance\Validator;

use App\Balance\Model\BalanceEntityInterface;

abstract class AbstractBalanceValidator implements ValidationInterface
{
    private $errors = [];

    public function isValid(): bool
    {
        return $this->isArrayEmpty($this->getErrors());
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function addError(string $name, string $message): void
    {
        $this->errors[$name] = $message;
    }

    public function isObjectExists(?BalanceEntityInterface $object): bool
    {
        return !$this->isNull($object);
    }

    public function isArrayEmpty(array $array): bool
    {
        return empty($array);
    }

    public function hasArrayKey(string $key, array $array): bool
    {
        return array_key_exists($key, $array);
    }

    public function isGreaterThanZero(float $number): bool
    {
        return ($number > 0.00);
    }

    public function isNull($value): bool
    {
        return is_null($value);
    }

    public function isFloat($value): bool
    {
        return is_float($value);
    }

    public function isInt($value): bool
    {
        return is_int($value);
    }

    public function isShorterThan(string $value, int $length): bool
    {
        return (strlen($value) < $length);
    }
}