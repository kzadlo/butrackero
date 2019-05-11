<?php

namespace App\Balance\Validator;

use App\Balance\Model\Expense;
use App\Balance\Model\ExpenseCategory;

final class ExpenseValidator extends AbstractBalanceValidator
{
    private const ERROR_NAME_EXPENSE = 'expense';
    private const ERROR_NAME_AMOUNT = 'amount';
    private const ERROR_NAME_CATEGORY = 'category';

    public function validate(array $expense): void
    {
        if (!$this->validateExpenseArray($expense)) {
            return;
        };

        $this->validateAmount($expense['amount']);
        $this->validateCategory($expense['category']);
    }

    public function validateExpenseArray(array $expense): bool
    {
        $isValid = true;

        if (!$this->hasArrayKey('amount', $expense)) {
            $this->addError(self::ERROR_NAME_AMOUNT, self::MESSAGE_KEY_NOT_EXISTS);

            $isValid = false;
        }

        if (!$this->hasArrayKey('category', $expense)) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_KEY_NOT_EXISTS);

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

    public function validateCategory(?string $category): bool
    {
        if ($this->isNull($category)) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isString($category)) {
            $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_IS_NOT_STRING);

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
