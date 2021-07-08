<?php

namespace app\core;

class Validator
{
    /**
     * Validate the email
     * @param string $email
     * @return array|string
     */
    public static function validateEmail(string $email)
    {
        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "The email is invalid!";
        }

        if (strlen($email) > 255) {
            $errors[] = "The email is too long!";
        }

        if (!empty($errors)) {
            return $errors;
        }

        return $email;
    }

    /**
     * Validate the string
     * @param string $string
     * @return array|string
     */
    public static function validateString(string $string)
    {
        $errors = [];

        if (!is_string($string)) {
            $errors[] = 'First parameter is not a string!';
        }

        $string = trim($string);
        $string = filter_var($string);
        $string = stripcslashes($string);
        $string = strip_tags($string);

        // Check that the string is not greater than 255 characters
        if (strlen($string) > 255) {
            $errors[] = "The '". $string ."' is too long!";
        }

        if (!empty($errors)) {
            return $errors;
        }

        return $string;
    }

    /**
     * Check if the errors array have
     * any errors and return the first error
     * @param mixed ...$errors
     * @return mixed
     */
    public static function checkErrors(...$errors)
    {
        foreach ($errors as $error) {
            if (is_array($error)) {
                return $error[0];
            }
        }
    }

    /**
     * Check if all the keys in the
     * array have value
     * @param array $arr
     * @param array $exceptions
     * @return bool
     */
    public static function allKeysFilled(array $arr, array $exceptions = [])
    {
        /**
         * If not all fields are required,
         * then remove this keys
         */
        if (!empty($exceptions)) {
            foreach ($exceptions as $exception) {
                if (isset($arr[$exception])) {
                    unset($arr[$exception]);
                }
            }
        }

        $count = count($arr);

        if (count(array_filter($arr)) != $count) {
            return false;
        }

        return true;
    }
}