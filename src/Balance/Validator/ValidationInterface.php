<?php

namespace App\Balance\Validator;

interface ValidationInterface
{
    const MESSAGE_OBJECT_NOT_EXISTS = "Object with this id doesn't exist";
    const MESSAGE_OBJECT_HAS_RELATION = "Object with this id has relation to other";

    const MESSAGE_ARRAY_IS_EMPTY = "This array can't be empty";

    const MESSAGE_KEY_NOT_EXISTS = "This key must declared";

    const MESSAGE_IS_LESS_OR_EQUAL_ZERO = "This value has to be greater than zero";
    const MESSAGE_IS_NULL = "This value can't be null";
    const MESSAGE_IS_NOT_FLOAT = "This value has to be float";
    const MESSAGE_IS_NOT_INT = "This value has to be int";
    const MESSAGE_IS_SHORTER_THAN = "This value has to shorter";

    public function validate(array $object): void;
}