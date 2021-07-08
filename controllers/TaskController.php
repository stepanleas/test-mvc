<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\core\Response;
use app\core\Session;
use app\core\Validator;
use app\models\Task;
use app\repositories\TaskRepository;

class TaskController extends Controller
{
    private TaskRepository $taskRepository;

    public function __construct()
    {
        $this->taskRepository = new TaskRepository('tasks');
    }

    /**
     * Index page with an array of tasks
     * @param Request $request
     * @return string|string[]
     */
    public function index(Request $request)
    {
        $body = $request->getBody();

        // Count the total number of tasks
        $countAllTasks = $this->taskRepository
            ->count();

        // Get the number or total pages
        $pageNo = 1;
        if (isset($body['page']) && is_numeric($body['page'])) {
            $pageNo = $body['page'];
        }

        // Set the number of tasks per page
        $perPage = 3;
        $totalPages = ceil($countAllTasks / $perPage);

        // Specify the order type
        $orderType = $body['orderType'] ?? 'DESC';
        if (isset($body['orderType']) && in_array(strtoupper($orderType), ['ASC', 'DESC'])) {
            $orderType = strtoupper($body['orderType']);
        }

        // Specify the column
        $column = 'id'; // Is displayed only in the index page, because the orderColumn can be an array
        $orderColumn = 'id'; // Default order column
        if (isset($body['column'])) {
            $lower = strtolower($body['column']);
            $orderColumn = $lower;
            $column = $lower;

            /**
             * If the specified column is of type status, then order
             * the records by 'completed' and 'admin_edited' columns
             */
            if ($column === 'status') {
                $orderColumn = ['completed','admin_edited'];
            }
        }

        // Get a list of tasks
        $tasks = $this->taskRepository
            ->getPaginatedAndOrderedTasks($perPage, $pageNo, $orderColumn, $orderType);

        // Get the auth user
        $user = Session::getAuthUser(['name','role']);

        return $this->render('tasks/index', [
            'tasks' => $tasks,
            'countAllTasks' => $countAllTasks,
            'totalPages' => $totalPages,
            'pageNo' => $pageNo,
            'orderType' => $orderType,
            'column' => $column,
            'user' => $user
        ]);
    }

    /**
     * Create the task
     * @param Request $request
     * @return false|string
     */
    public function store(Request $request)
    {
        // Verify if the user is authorized
        $redirect = $this->redirectUser();
        if (!is_bool($redirect)) {
            die;
        }

        return $this->save($request->getBody());
    }

    /**
     * Update the task
     * @param Request $request
     * @return false|string
     */
    public function update(Request $request)
    {
        // Verify if the user is authorized
        $redirect = $this->redirectUser();
        if (!is_bool($redirect)) {
            die;
        }

        $body = $request->getBody();
        $body['admin_edited'] = 1;
        return $this->save($body);
    }

    /**
     * Create or update the task
     * @param $body
     * @return false|string
     */
    private function save($body)
    {
        // Verify that all the fields are not empty
        $checkFields = Validator::allKeysFilled($body, ['completed']);
        if ($checkFields === false) {
            return Response::sendJsonMessage('All fields are required!', 'danger');
        }

        // Validate the input data
        $user_name = Validator::validateString($body['user_name']);
        $user_email = Validator::validateEmail($body['user_email']);
        $description = Validator::validateString($body['description']);

        $taskId = $body['id'] ?? null;
        $completed = $body['completed'] ?? null;
        $admin_edited = $body['admin_edited'] ?? null;

        // Verify that the completed status is equal to 1
        if (!is_null($completed) && !in_array($completed, [0,1])) {
            return Response::sendJsonMessage("Some error occurred, please refresh the page", 'danger');
        }

        // Verify if there are any errors in the validation
        $errorMessage = Validator::checkErrors($user_name, $user_email, $description);
        if (is_string($errorMessage)) {
            return Response::sendJsonMessage($errorMessage, 'danger');
        }

        // Create an instance of the task
        $task = new Task([
            'id' => $taskId,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'description' => $description,
            'completed' => $completed,
            'admin_edited' => $admin_edited
        ]);

        try {
            // Store the model in the database
            $task->save();
            // Write a message in the session
            $message = 'Task created successfully';
            if (!is_null($taskId)) {
                $message = 'Task updated successfully';
            }

            Session::set('message', $message);
            return Response::sendJsonMessage($message);
        } catch (\Exception $e) {
            return Response::sendJsonMessage($e->getMessage(), 'danger');
        }
    }

    /**
     * Delete the task by id
     * @param Request $request
     * @return false|string
     */
    public function delete(Request $request)
    {
        // Verify if the user is authorized
        $redirect = $this->redirectUser();
        if (!is_bool($redirect)) {
            die;
        }

        $body = $request->getBody();

        if (isset($body['id']) && is_numeric($body['id'])) {
            $this->taskRepository
                ->delete($body['id']);

            return Response::sendJsonMessage('Task deleted');
        }

        return Response::sendJsonMessage('An error occurred, please refresh the page', 'danger');
    }

    /**
     * Page for creating the task
     * @return string|string[]
     */
    public function create()
    {
        // Verify if the user is authorized
        $redirect = $this->redirectUser();
        if (!is_bool($redirect)) {
            die;
        }

        return $this->render('tasks/store');
    }

    /**
     * Display the task in the store page
     * @param Request $request
     * @return string|string[]
     */
    public function edit(Request $request)
    {
        // Verify if the user is authorized
        $redirect = $this->redirectUser();
        if (!is_bool($redirect)) {
            die;
        }

        $body = $request->getBody();

        // Get the specific fields from the task
        $fields = [
            'user_name',
            'user_email',
            'description',
            'completed'
        ];

        $task = $this->taskRepository
            ->find($body['id'], $fields);

        return $this->render('tasks/store', [
            'task' => $task
        ]);
    }

    /**
     * Verify if the user is authenticated,
     * if not, redirect him to the main page
     * @return false|string
     */
    private function redirectUser()
    {
        // Verify if the user is authorized
        if (!Session::userIsAdmin()) {
            if (Application::$app->request->method() === 'GET') {
                header('Location: /');
            }
            return Response::sendJsonMessage('The user is not authorized', 'redirect');
        }
        return false;
    }
}