<?php

namespace App\Balance\Validator;

interface ValidationInterface
{
    public const MESSAGE_OBJECT_NOT_EXISTS = "Object with this id doesn't exist";
    public const MESSAGE_OBJECT_HAS_RELATION = "Object with this id has relation to other";

    public const MESSAGE_ARRAY_IS_EMPTY = "This array can't be empty";

    public const MESSAGE_KEY_NOT_EXISTS = "This key must declared";

    public const MESSAGE_IS_LESS_OR_EQUAL_ZERO = "This value has to be greater than zero";
    public const MESSAGE_IS_NULL = "This value can't be null";
    public const MESSAGE_IS_NOT_FLOAT = "This value has to be float";
    public const MESSAGE_IS_NOT_INT = "This value has to be int";
    public const MESSAGE_IS_NOT_STRING = "This value has to be string";
    public const MESSAGE_IS_SHORTER_THAN = "This value has to shorter";
    public const MESSAGE_IS_LONGER_THAN = "This value has to longer";

    public function validate(array $object): void;
}
