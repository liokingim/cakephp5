<?php

namespace App\Model\Validation;

class Rules
{
    public static function validNumber($value, $context)
    {
        return preg_match('/^[0-9]+$/', $value);
    }

    public static function validateEmail($value, $context)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}

?>
