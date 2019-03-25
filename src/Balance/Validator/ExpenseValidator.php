<?php

namespace App\Balance\Validator;

use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;

class ExpenseValidator extends AbstractBalanceValidator
{
    const ERROR_NAME_EXPENSE = 'expense';
    const ERROR_NAME_AMOUNT = 'amount';
    const ERROR_NAME_CATEGORY = 'category';

    public function validate(array $expense): void
    {
        $this->validateAmount($expense);
        $this->validateCategory($expense);
    }

    public function validateAmount(array $expense): bool
    {
        if (!$this->hasArrayKey('amount', $expense)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_KEY_NOT_EXISTS);

            return false;
        }

        if ($this->isNull($expense['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isFloat($expense['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_NOT_FLOAT);

            return false;
        }

        if (!$this->isGreaterThanZero($expense['amount'])) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_IS_LESS_OR_EQUAL_ZERO);

            return false;
        }

        return true;
    }

    public function validateCategory(array $expense): bool
    {
        if (!$this->hasArrayKey('category', $expense)) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_KEY_NOT_EXISTS);

            return false;
        }

        if ($this->isNull($expense['category'])) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isInt($expense['category'])) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_NOT_INT);

            return false;
        }

        if (!$this->isGreaterThanZero($expense['category'])) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_LESS_OR_EQUAL_ZERO);

            return false;
        }

        return true;
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