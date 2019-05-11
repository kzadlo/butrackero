<?php

namespace App\Balance\Validator;

use App\Balance\Model\ExpenseCategory;

final class CategoryValidator extends AbstractBalanceValidator
{
    private const ERROR_NAME_CATEGORY = 'category';
    private const ERROR_NAME_NAME = 'name';
    private const ERROR_NAME_DESCRIPTION = 'description';

    public function validate(array $category): void
    {
        if (!$this->validateCategoryArray($category)) {
            return;
        };

        $this->validateName($category['name']);
        $this->validateDescription($category['description']);
    }

    public function validateCategoryArray(array $category): bool
    {
        $isValid = true;

        if (!$this->hasArrayKey('name', $category)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_KEY_NOT_EXISTS);

            $isValid = false;
        }

        if (!$this->hasArrayKey('description', $category)) {
            $this->addError(self::ERROR_NAME_DESCRIPTION, self::MESSAGE_KEY_NOT_EXISTS);

            $isValid = false;
        }

        return $isValid;
    }

    public function validateName(?string $name): bool
    {
        if ($this->isNull($name)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isShorterThan($name, 128)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_IS_SHORTER_THAN);

            return false;
        }

        return true;
    }

    public function validateDescription(?string $description): bool
    {
        if (!$this->isShorterThan($description, 255)) {
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
}
