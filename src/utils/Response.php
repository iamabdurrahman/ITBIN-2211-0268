<?php

class Response
{
    public static function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    public static function success($message, $data = [], $statusCode = 200)
    {
        http_response_code($statusCode);
        self::json(['success' => true, 'message' => $message, 'data' => $data], $statusCode);
    }

    public static function error($message, $statusCode = 400)
    {
        self::json(['success' => false, 'message' => $message], $statusCode);
    }
}

?>