<?php
require_once __DIR__ . '/../../config/env.php';

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }

    public static function destroy()
    {
        session_destroy();
        $_SESSION = [];
    }

    public static function regenerate()
    {
        session_regenerate_id(true);
    }

    public static function checkSessionExpiry()
    {
        $timeout_duration = getenv('SESSION_EXPIRY') ? getenv('SESSION_EXPIRY') : 1800;

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
            self::destroy();
            header('Location: login.php');
            exit;
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

?>