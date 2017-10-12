<?php
namespace Bx
{
    Validator::setRule('pattern', function($value, $pattern) {
        $result = preg_match($pattern, $value);
        return !!$result;
    });
}