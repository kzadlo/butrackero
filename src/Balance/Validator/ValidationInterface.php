<?php

namespace App\Balance\Validator;

interface ValidationInterface
{
    CONST MESSAGE_OBJECT_NOT_EXISTS = "Object with this id doesn't exist";
    CONST MESSAGE_OBJECT_HAS_RELATION = "Object with this id has relation to other";

    CONST MESSAGE_ARRAY_IS_EMPTY = "This array can't be empty";

    CONST MESSAGE_KEY_NOT_EXISTS = "This key must declared";

    CONST MESSAGE_IS_LESS_OR_EQUAL_ZERO = "This value has to be greater than zero";
    CONST MESSAGE_IS_NULL = "This value can't be null";
    CONST MESSAGE_IS_NOT_FLOAT = "This value has to be float";
    CONST MESSAGE_IS_NOT_INT = "This value has to be int";
    CONST MESSAGE_IS_SHORTER_THAN = "This value has to shorter";

    public function validate(array $object): void;
}