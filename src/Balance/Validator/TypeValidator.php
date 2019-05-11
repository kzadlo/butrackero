<?php

namespace App\Balance\Validator;

use App\Balance\Model\IncomeType;

final class TypeValidator extends AbstractBalanceValidator
{
    private const ERROR_NAME_TYPE = 'type';
    private const ERROR_NAME_NAME = 'name';
    private const ERROR_NAME_DESCRIPTION = 'description';

    public function validate(array $type): void
    {
        if (!$this->validateTypeArray($type)) {
            return;
        };

        $this->validateName($type['name']);
        $this->validateDescription($type['description']);
    }

    public function validateTypeArray(array $type): bool
    {
        $isValid = true;

        if (!$this->hasArrayKey('name', $type)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_KEY_NOT_EXISTS);

            $isValid = false;
        }

        if (!$this->hasArrayKey('description', $type)) {
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

        if (!$this->isLongerThan($name, 2)) {
            $this->addError(self::ERROR_NAME_NAME, self::MESSAGE_IS_LONGER_THAN);

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

    public function validateTypeExists(?IncomeType $type): void
    {
        if (!$this->isObjectExists($type)) {
            $this->addError(self::ERROR_NAME_TYPE, self::MESSAGE_OBJECT_NOT_EXISTS);
        }
    }
}
