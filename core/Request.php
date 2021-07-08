<?php

namespace app\core;

class Request
{
    /**
     * Get the URL path
     * @return false|mixed|string
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        // Verify if the url path have question mark
        $position = strpos($path, '?');

        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    /**
     * Get the request method
     * @return mixed
     */
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Dump and die
     * @param mixed ...$params
     */
    public static function dd(...$params)
    {
        foreach ($params as $param) {
            if (is_array($param) || is_object($param)) {
                echo '<pre>'; print_r($param); echo '</pre>';
            } else {
                echo $param . '<br><br>';
            }
        }
        die;
    }

    /**
     * Get the body of the request method
     * @return array
     */
    public function getBody()
    {
        $getData = $this->getValidatedData($_GET);
        $postData = $this->getValidatedData($_POST);

        return array_merge($getData, $postData);
    }

    /**
     * Validate the global data and
     * return it
     * @param $global
     * @return array
     */
    private function getValidatedData($global)
    {
        $body = [];

        foreach ($global as $key => $value) {
            $validated = stripcslashes(trim($value));
            $body[$key] = filter_var($validated);
        }

        return $body;
    }
}