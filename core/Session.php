<?php

namespace app\core;

session_start();

class Session
{
    /**
     * Start a new session
     */
    public static function start()
    {
        if (!isset($_SESSION)) {
            session_set_cookie_params(0);
            session_start();
        }
    }

    /**
     * Verify if the user is authorized
     * @return bool
     */
    public static function userIsAuthorized()
    {
        if (isset($_SESSION['user'])) {
            return true;
        }
        return false;
    }

    /**
     * Verify if the user is admin
     * @return bool
     */
    public static function userIsAdmin()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
            return true;
        }
        return false;
    }

    /**
     * Get the auth user
     * @param array $fields
     * @return bool|mixed
     */
    public static function getAuthUser($fields = [])
    {
        if (isset($_SESSION['user'])) {
            $authUser = [];
            if (!empty($fields)) {
                foreach ($fields as $field) {
                    if (isset($_SESSION['user'][$field])) {
                        $authUser[$field] = $_SESSION['user'][$field];
                    }
                }
            } else {
                $authUser = $_SESSION['user'];
            }

            return $authUser;
        }

        return false;
    }

    /**
     * Get a value from the session
     * @param $key
     * @return bool|mixed
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return false;
    }

    /**
     * Set a new value in the session
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}