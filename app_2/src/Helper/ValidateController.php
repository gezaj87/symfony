<?php

namespace App\Helper;

use Exception;

class ValidationHelper
{

    const EMAIL_NOT_VALID = 'Email is not valid';

    const NAME_TOO_SHORT = 'Name is too short';
    const NAME_TOO_LONG = 'Name is too long';
    const INVALID_CHARACTERS = 'Name contains invalid characters';

    const MIN_PASSWORD_LENGTH = 4;
    const MAX_PASSWORD_LENGTH = 255;
    const PASSWORD_TOO_SHORT = 'Password is too short';
    const PASSWORD_TOO_LONG = 'Password is too long';
    const PASSWORDS_NOT_MATCH = 'Passwords do not match';

    const MISSING_INPUT = 'Missing input';

    public static function validateEmail($email)
    {
        try
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                throw new Exception(self::EMAIL_NOT_VALID);
            }
            
        }
        catch (Exception $e)
        {
            throw $e;
        }

    }

    public static function validateName($name)
    {

        try
        {
            if (strlen($name) < 4)
            {
                throw new Exception(self::NAME_TOO_SHORT);
            }

            if (strlen($name) > 255)
            {
                throw new Exception(self::NAME_TOO_LONG);
            }

            if (!preg_match('/^[a-zA-Z0-9\s]+$/', $name))
            {
                throw new Exception(self::INVALID_CHARACTERS);
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public static function validatePassword($password, $password2)
    {
        try
        {
            if (strlen($password) < self::MIN_PASSWORD_LENGTH)
            {
                throw new Exception(self::PASSWORD_TOO_SHORT);
            }

            if (strlen($password) > self::MAX_PASSWORD_LENGTH)
            {
                throw new Exception(self::PASSWORD_TOO_LONG);
            }

            if ($password !== $password2)
            {
                throw new Exception(self::PASSWORDS_NOT_MATCH);
            }
            
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    public static function validateMissingInput(array $inputs)
    {
        try
        {
            foreach ($inputs as $input)
            {
                if (empty($input))
                {
                    throw new Exception(self::MISSING_INPUT);
                }
            }
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }
}