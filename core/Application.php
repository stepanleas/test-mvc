<?php

namespace app\core;

class Application
{
    public Router $router;
    public Request $request;
    public Response $response;

    public Controller $controller;
    public Database $db;

    public static Application $app;
    public static $ROOT_DIR;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->controller = new Controller();
        $this->db = new Database();

        $this->router = new Router($this->request, $this->response);

        self::$app = $this;
        self::$ROOT_DIR = dirname(__DIR__);

        // Start the session
        Session::start();
    }

    public function run()
    {
        echo $this->router->resolve();
    }
}