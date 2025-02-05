<?php
namespace App\Utils;

use App\Utils\Exception\ValidationException;

class Validator
{
    public static function validateArray_AllMustExist(array $array, array $specs, ?string $message = null): void
    {
        foreach ($specs as $key => $path) {
            if (!array_key_exists($key, $array)) {
                throw new ValidationException($key, 'is missing', $message);
            }
            self::validate($array[$key], $path, $key, $message);
        }
    }

    public static function validate(mixed &$value, string $path, string $keyName, ?string $message): void
    {
        switch ($path) {
            case 'users.email':
                self::assertNotEmpty($value, $path, $keyName, $message);
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException($keyName, 'is invalid', $message);
                }
                break;

            case 'users.password':
                self::assertNotEmpty($value, $path, $keyName, $message);
                $N = strlen($value);
                if ($N < 8) {
                    throw new ValidationException($keyName, 'is too short (minimum 8 characters, maximum 24 characters)', $message);
                }
                else if ($N > 24) {
                    throw new ValidationException($keyName, 'is too long (minimum 8 characters, maximum 24 characters)', $message);
                }
                // https://stackoverflow.com/a/6497946/13680015
                if (preg_match('/[^\x20-\x7e]/', $value)) {
                    throw new ValidationException($keyName, 'contains invalid characters', $message);
                }
                break;
            
            case 'users.name':
                self::assertNotEmpty($value, $path, $keyName, $message);
                break;
        }
    }

    private static function assertNotEmpty(mixed &$value, string $path, string $keyName, ?string $message): void
    {
        if (!$value) {
            throw new ValidationException($keyName, 'is empty', $message);
        }
    }
}
