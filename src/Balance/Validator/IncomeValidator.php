<?php

namespace App\Balance\Validator;

use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;

class IncomeValidator extends AbstractBalanceValidator
{
    private const ERROR_NAME_INCOME = 'income';
    private const ERROR_NAME_AMOUNT = 'amount';
    private const ERROR_NAME_TYPE = 'type';

    public function validate(array $income): void
    {
        if (!$this->validateIncomeArray($income)) {
            return;
        };

        $this->validateAmount($income['amount']);
        $this->validateType($income['type']);
    }

    public function validateIncomeArray(array $income): bool
    {
        $isValid = true;

        if (!$this->hasArrayKey('amount', $income)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_KEY_NOT_EXISTS);

            $isValid = false;
        }

        if (!$this->hasArrayKey('type', $income)) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_KEY_NOT_EXISTS);

            $isValid = false;
        }

        return $isValid;
    }

    public function validateAmount(?float $amount): bool
    {
        if ($this->isNull($amount)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isFloat($amount)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NOT_FLOAT);

            return false;
        }

        if (!$this->isGreaterThanZero($amount)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_LESS_OR_EQUAL_ZERO);

            return false;
        }

        return true;
    }

    public function validateType(?string $type): bool
    {
        if ($this->isNull($type)) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isString($type)) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_IS_NOT_STRING);

            return false;
        }

        return true;
    }

    public function validateTypeExists(?IncomeType $type): void
    {
        if (!$this->isObjectExists($type)) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_OBJECT_NOT_EXISTS);
        }
    }

    public function validateIncomeExists(?Income $income): void
    {
        if (!$this->isObjectExists($income)) {
            $this->addError(self::ERROR_NAME_INCOME, self::MESSAGE_OBJECT_NOT_EXISTS);
        }
    }
}
