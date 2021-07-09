<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Password;
use app\core\Request;
use app\core\Response;
use app\core\Session;
use app\core\Validator;
use app\models\User;
use app\repositories\UserRepository;

class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository('users');
    }

    public function loginIndex()
    {
        return $this->render('auth/login');
    }

    public function registerIndex()
    {
        return $this->render('auth/register');
    }

    /**
     * Login the user
     * @param Request $request
     * @return false|string
     */
    public function login(Request $request)
    {
        $body = $request->getBody();

        // Verify that all the fields are not empty
        if (!Validator::allKeysFilled($body)) {
            return Response::sendJsonMessage('All fields are required!', 'danger');
        }

        // Validate the name and the password
        $name = Validator::validateString($body['name']);
        $password = Validator::validateString($body['password']);

        // Verify if there are any errors in the validation
        $errorMessage = Validator::checkErrors($name, $password);
        if (is_string($errorMessage)) {
            return Response::sendJsonMessage($errorMessage, 'danger');
        }

        // Search the user by providing his name
        $user = $this->userRepository
            ->findUserByName($name);

        // If an user with such name doesn't exists, return an error message
        if (!$user) {
            return Response::sendJsonMessage('An user with such name doesn\'t exist!', 'danger');
        }

        // Verify that the passwords match
        if (Password::encrypt($password) != $user['password']) {
            return Response::sendJsonMessage('The password is incorrect!', 'danger');
        }

        // Set the user in the session
        $this->loginUser($user);
        return Response::sendJsonMessage('Logged in!');
    }

    /**
     * Register a new user
     * @param Request $request
     * @return false|string
     */
    public function register(Request $request)
    {
        $body = $request->getBody();

        // Verify that all the fields are not empty
        $checkFields = Validator::allKeysFilled($body);
        if (!$checkFields) {
            return Response::sendJsonMessage('All fields are required!', 'danger');
        }

        // Validate the data
        $name = Validator::validateString($body['name']);
        $email = Validator::validateEmail($body['email']);
        $validatePassword = Password::comparePasswords($body['password'], $body['confirm_password']);

        // Verify if there are any errors in the validation
        $errorMessage = Validator::checkErrors($name, $email, $validatePassword);
        if (is_string($errorMessage)) {
            return Response::sendJsonMessage($errorMessage, 'danger');
        }

        // Encrypt the password
        $password = Password::encrypt($validatePassword);

        // Verify if there is an user with such name
        $findUser = $this->userRepository
            ->findUserByName($name, ['name']);

        if ($findUser['name'] == $name) {
            return Response::sendJsonMessage('An user with such name already exists!', 'danger');
        }

        // Create an instance of the user
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'user'
        ]);

        try {
            // Save the user in the database
            $registeredUser = Application::$app->db->save($user);

            if (is_array($registeredUser)) {
                // Login the user in the session
                $this->loginUser($registeredUser);
                return Response::sendJsonMessage('User created');
            }
        } catch (\ReflectionException $e) {
            return Response::sendJsonMessage($e->getMessage(), 'danger');
        }
    }

    /**
     * Authorize the user
     * @param $user
     */
    private function loginUser($user)
    {
        // Remove the password
        unset($user['password']);
        // Put the user inside the session
        Session::set('user', $user);
    }

    /**
     * Verify if the user is logged in or not
     * @return false|string
     */
    public function checkAuthUser()
    {
        return Response::sendJsonResponse(Session::userIsAuthorized());
    }

    /**
     * Verify if the session have an message
     * to the user and remove it
     * @return bool|mixed
     */
    public function checkSessionMessage()
    {
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
            return Response::sendJsonMessage($message);
        }

        return Response::sendJsonMessage('No messages in session', 'danger');
    }

    /**
     * Destroys the current session
     * and logout the user
     */
    public function logout()
    {
        session_destroy();
        header('Location: /');
    }
}