<?php

namespace App\Balance\Validator;

use App\Balance\Model\IncomeType;

class TypeValidator extends AbstractBalanceValidator
{
    CONST ERROR_NAME_TYPE = 'type';
    CONST ERROR_NAME_NAME = 'name';
    CONST ERROR_NAME_DESCRIPTION = 'description';

    public function validate(array $type): void
    {
        $this->validateName($type);
        $this->validateDescription($type);
    }

    public function validateName(array $type): bool
    {
        if (!$this->hasArrayKey('name', $type)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_KEY_NOT_EXISTS);

            return false;
        }

        if ($this->isNull($type['name'])) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_IS_NULL);

            return false;
        }

        if (!$this->isShorterThan($type['name'], 128)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_IS_SHORTER_THAN);

            return false;
        }

        return true;
    }

    public function validateDescription(array $type): bool
    {
        if (!$this->hasArrayKey('description', $type)) {
            $this->addError(self::ERROR_NAME_DESCRIPTION, self::MESSAGE_KEY_NOT_EXISTS);

            return false;
        }

        if (!$this->isShorterThan($type['description'], 255)) {
            $this->addError(self::ERROR_NAME_DESCRIPTION, self::MESSAGE_IS_SHORTER_THAN);

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

    public function validateTypeHasIncomes(?IncomeType $type): void
    {
        if ($this->isObjectExists($type)) {
            if (!$this->isArrayEmpty($type->getIncomes()->toArray())) {
                $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_OBJECT_HAS_RELATION);
            }
        }
    }
}