<?php

namespace App\Balance\Validator;

use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;

class ExpenseValidator extends AbstractBalanceValidator
{
    CONST ERROR_NAME_EXPENSE = 'expense';
    CONST ERROR_NAME_AMOUNT = 'amount';
    CONST ERROR_NAME_CATEGORY = 'category';

    public function validate(array $expense): void
    {
        if ($this->isArrayEmpty($expense)) {
            $this->addError(self::ERROR_NAME_EXPENSE, self::MESSAGE_ARRAY_IS_EMPTY);
        } else {
            $this->validateAmount($expense)->validateCategory($expense);
        }
    }

    public function validateAmount(array $expense): ExpenseValidator
    {
        if (!$this->hasArrayKey('amount', $expense)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_KEY_NOT_EXISTS);

            return $this;
        }

        if ($this->isNull($expense['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NULL);

            return $this;
        }

        if (!$this->isFloat($expense['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NOT_FLOAT);

            return $this;
        }

        if (!$this->isGreaterThanZero($expense['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_LESS_OR_EQUAL_ZERO);

            return $this;
        }

        return $this;
    }

    public function validateCategory(array $expense): ExpenseValidator
    {
        if (!$this->hasArrayKey('category', $expense)) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_KEY_NOT_EXISTS);

            return $this;
        }

        if ($this->isNull($expense['category'])) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_NULL);

            return $this;
        }

        if (!$this->isInt($expense['category'])) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_NOT_INT);

            return $this;
        }

        if (!$this->isGreaterThanZero($expense['category'])) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_LESS_OR_EQUAL_ZERO);

            return $this;
        }

        return $this;
    }

    public function validateCategoryExists(?ExpenseCategory $category): void
    {
        if (!$this->isObjectExists($category)) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_OBJECT_NOT_EXISTS);
        }
    }

    public function validateExpenseExists(?Expense $expense): void
    {
        if (!$this->isObjectExists($expense)) {
            $this->addError(self::ERROR_NAME_EXPENSE, self::MESSAGE_OBJECT_NOT_EXISTS);
        }
    }
}