<?php

namespace App\Balance\Validator;

use App\Balance\Model\Income;
use App\Balance\Model\IncomeType;

class IncomeValidator extends AbstractBalanceValidator
{
    CONST ERROR_NAME_INCOME = 'income';
    CONST ERROR_NAME_AMOUNT = 'amount';
    CONST ERROR_NAME_TYPE = 'type';

    public function validate(array $income): void
    {
        if ($this->isArrayEmpty($income)) {
            $this->addError(self::ERROR_NAME_INCOME, self::MESSAGE_ARRAY_IS_EMPTY);
        } else {
            $this->validateAmount($income)->validateType($income);
        }
    }

    public function validateAmount(array $income): IncomeValidator
    {
        if (!$this->hasArrayKey('amount', $income)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_KEY_NOT_EXISTS);

            return $this;
        }

        if ($this->isNull($income['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NULL);

            return $this;
        }

        if (!$this->isFloat($income['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NOT_FLOAT);

            return $this;
        }

        if (!$this->isGreaterThanZero($income['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_LESS_OR_EQUAL_ZERO);

            return $this;
        }

        return $this;
    }

    public function validateType(array $income): IncomeValidator
    {
        if (!$this->hasArrayKey('type', $income)) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_KEY_NOT_EXISTS);

            return $this;
        }

        if ($this->isNull($income['type'])) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_IS_NULL);

            return $this;
        }

        if (!$this->isInt($income['type'])) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_IS_NOT_INT);

            return $this;
        }

        if (!$this->isGreaterThanZero($income['type'])) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_IS_LESS_OR_EQUAL_ZERO);

            return $this;
        }

        return $this;
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