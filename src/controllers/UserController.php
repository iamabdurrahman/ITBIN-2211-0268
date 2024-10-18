<?php
session_start();
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Session.php';
Session::start();

class UserController
{

    public function registerUser($data)
    {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            Response::error("Please provide all required fields.", $statusCode=400);
        }

        try {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);
            $user->setName($data['name']);
            $result = $user->create();

            if ($result) {
                Response::success("User registered successfully!");
            } else {
                Response::error("Failed to register user.", $statusCode=200);
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function loginUser($data)
    {
        try {
            $user = new User();
            $result = $user->login($data['email'], $data['password']);
            if ($result['success']) {
                Session::set('user', $result['user']);
                Response::success("User logged in successfully!");
            } else {
                Response::error($result['message'],$statusCode=200);
            }
        } catch (Exception $e) {
            Response::error($e->getMessage(),$statusCode=500);
        }
    }

    public function getUsers()
    {
        try {
            $user = new User();
            $users = $user->getAll();
            Response::success("Users retrieved successfully.", $users);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function getUserById($id)
    {
        try {
            $user = new User();
            $user->setId($id);
            $userData = $user->getById();

            if ($userData) {
                Response::success("User retrieved successfully.", $userData);
            } else {
                Response::error("User not found.", 404);
            }
        } catch (Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = new User();
            $user->setId($id);
            $result = $user->delete();

            if ($result) {
                Response::success("User deleted successfully!");
            } else {
                Response::error("Failed to delete user.");
            }
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }

    public function updateUser($data)
    {
        if (!isset($data['user_id']) || !isset($data['email']) || !isset($data['name']) || !isset($data['role'])) {
            Response::error("Please provide all required fields.",  400);
        }

        try {
            $user = new User();
            $user->setId($data['user_id']);
            $user->setEmail($data['email']);
            $user->setName($data['name']);
            $user->setRole($data['role']);
            $result = $user->update();

            if ($result) {
                Response::success("User updated successfully!");
            } else {
                Response::error("Failed to update user.", $statusCode = 200);
            }
        } catch (Exception $e) {
            Response::error($e->getMessage(), $statusCode = 500);
        }
    }

    public function updatePassword($data)
    {
        if (!isset($data['id']) || !isset($data['old_password']) || !isset($data['new_password'])) {
            Response::error("Please provide all required fields.", 400);
        }

        try {
            $user = new User();
            $user->setId($data['id']);

            $currentUser = $user->getById();
            if (!$currentUser) {
                Response::error("User not found.", 404);
            }

            if($currentUser['password'] != md5($data['old_password'])) {
                Response::error("Old password is incorrect.", 200);
            }

            $user->setPassword($data['new_password']);
            $result = $user->updatePassword();

            if ($result) {
                Response::success("Password updated successfully!");
            } else {
                Response::error("Failed to update password.", 200);
            }
        } catch (Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }

    public function getBalance()
    {
        try {
            $userId = Session::get('user')['id'];
            if (!$userId) {
                throw new Exception('User not logged in');
            }
            $user = new User();
            $balance = $user->getBalanceById($userId);
            Response::success("Balance retrieved successfully.", ['balance' => $balance]);
        } catch (Exception $e) {
            Response::error($e->getMessage());
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new UserController();
    $action = $_GET['action'];
    $data = $_POST;

    switch ($action) {
        case 'login':
            $controller->loginUser($data);
            break;

        case 'register':
            $controller->registerUser($data);
            break;
        case 'getUsers':
            $controller->getUsers();
            break;

        case 'getById':
            $id = $_GET['id'];
            $controller->getUserById($id);
            break;
        case 'updateUser':
            $id = $_POST['id'];
            $controller->updateUser($data);
            break;
        case 'updatePassword':
            $controller->updatePassword($data);
            break;
        case 'delete':
            $id = $_POST['id'];
            $controller->deleteUser($id);
            break;

        case 'getBalance':
            $controller->getBalance();
            break;
    }
}

?>