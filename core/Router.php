<?php

namespace app\core;

class Router
{
    public array $routes;
    public $request;
    public $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * GET request method
     * @param $path
     * @param $callback
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    /**
     * POST request method
     * @param $path
     * @param $callback
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    /**
     * Resolve the callback function
     * @return mixed|string
     */
    public function resolve()
    {
        // Get the path and the request method
        $path = $this->request->getPath();
        $method = $this->request->method();

        // Verify if the callback is set in the routes
        $callback = $this->routes[$method][$path] ?? false;

        // If the page was not found, then throw an error
        if ($callback === false) {
            $this->response->setStatusCode(404);
            return $this->renderView("exceptions/404");
        }

        // If the callback is a string, then render the view
        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        /**
         * If the callback is an array containing
         * the controller name, then create an
         * instance of it
         */
        if (is_array($callback)) {
            $callback[0] = new $callback[0]();
        }

        return call_user_func($callback, $this->request);
    }

    /**
     * Render the view
     * @param $view
     * @param array $params
     * @return string|string[]
     */
    public function renderView($view, $params = [])
    {
        $params['root_dir'] = Application::$ROOT_DIR;

        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view, $params);

        return str_replace('{{ content }}', $viewContent, $layoutContent);
    }

    /**
     * Include the layout view
     * @return false|string
     */
    protected function layoutContent()
    {
        ob_start();
        include_once(Application::$ROOT_DIR . '/views/layouts/main.php');
        return ob_get_clean();
    }

    /**
     * Include the page from the views folder
     * @param $view
     * @param $params
     * @return false|string
     */
    protected function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include_once(Application::$ROOT_DIR . '/views/'. $view .'.php');
        return ob_get_clean();
    }
}