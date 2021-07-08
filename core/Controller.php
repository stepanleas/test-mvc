<?php

namespace app\core;

class Controller
{
    /**
     * Render the view with the params
     * @param $view
     * @param array $params
     * @return string|string[]
     */
    public function render($view, $params = [])
    {
        return Application::$app->router
            ->renderView($view, $params);
    }
}