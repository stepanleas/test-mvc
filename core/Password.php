<?php

namespace app\core;

/**
 * Class to work with passwords
 * Class Password
 * @package app\core
 */
class Password
{
    /**
     * Encrypt the password
     * @param $password
     * @return string
     */
    public static function encrypt($password)
    {
        return md5($password);
    }

    /**
     * Encrypt the password
     * @param $password
     * @param $confirmPassword
     * @return array
     */
    public static function comparePasswords($password, $confirmPassword)
    {
        $errors = [];
        if ($password != $confirmPassword) {
            $errors[] = 'The passwords doesn\'t match!';
        }

        if (!empty($errors)) {
            return $errors;
        }

        return $password;
    }
}