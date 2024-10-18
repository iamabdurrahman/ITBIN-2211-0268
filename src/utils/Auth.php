<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/Session.php';

class Auth
{
    public static function login($email, $password)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if (password_verify($password, $user['password'])) {
            return ['success' => true, 'user' => $user];
        }

        return ['success' => false, 'message' => 'Invalid email or password.'];
    }

    public static function logout()
    {
        Session::destroy();
    }

    public static function isAdmin()
    {
        return Session::get('user')['role'] === 'admin';
    }
}

?>
