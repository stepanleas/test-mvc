<?php

require_once('./vendor/autoload.php');

use app\controllers\AuthController;
use app\controllers\TaskController;
use app\core\Application;

$app = new Application();

// AuthController
$app->router->get('/login', [AuthController::class, 'loginIndex']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->get('/register', [AuthController::class, 'registerIndex']);
$app->router->post('/register', [AuthController::class, 'register']);
$app->router->get('/logout', [AuthController::class, 'logout']);

// Session
$app->router->get('/check_auth_user', [AuthController::class, 'checkAuthUser']);
$app->router->get('/check_session_message', [AuthController::class, 'checkSessionMessage']);
// !Session

// !AuthController

// TaskController
$app->router->get('/', [TaskController::class, 'index']);
$app->router->get('/create', [TaskController::class, 'create']);
$app->router->get('/edit', [TaskController::class, 'edit']);
$app->router->post('/store', [TaskController::class, 'store']);
$app->router->post('/update', [TaskController::class, 'update']);
$app->router->post('/delete', [TaskController::class, 'delete']);
$app->router->post('/paginate', [TaskController::class, 'paginateTasks']);
// !TaskController

$app->run();