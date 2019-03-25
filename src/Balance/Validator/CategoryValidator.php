<?php

namespace App\Balance\Validator;

use App\Balance\Model\ExpenseCategory;

class CategoryValidator extends AbstractBalanceValidator
{
    const ERROR_NAME_CATEGORY = 'category';
    const ERROR_NAME_NAME = 'name';
    const ERROR_NAME_DESCRIPTION = 'description';

    public function validate(array $category): void
    {
        $this->validateName($category);
        $this->validateDescription($category);
    }

    public function validateName(array $category): bool
    {
        if (!$this->hasArrayKey('name', $category)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_KEY_NOT_EXISTS);

            return false;
        }

        if ($this->isNull($category['name'])) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isShorterThan($category['name'], 128)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_IS_SHORTER_THAN);

            return false;
        }

        return true;
    }

    public function validateDescription(array $category): bool
    {
        if (!$this->hasArrayKey('description', $category)) {
            $this->addError(self::ERROR_NAME_DESCRIPTION, self::MESSAGE_KEY_NOT_EXISTS);

            return false;
        }

        if (!$this->isShorterThan($category['description'], 255)) {
            $this->addError(self::ERROR_NAME_DESCRIPTION, self::MESSAGE_IS_SHORTER_THAN);

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

    public function validateCategoryHasExpenses(?ExpenseCategory $category): void
    {
        if ($this->isObjectExists($category)) {
            if (!$this->isArrayEmpty($category->getExpenses()->toArray())) {
                $this->addError(self::ERROR_NAME_CATEGORY, self::MESSAGE_OBJECT_HAS_RELATION);
            }
        }
    }
}
