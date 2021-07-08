<?php

namespace app\core;

class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    /**
     * Send a json response to the user
     * containing the message and the message type
     * @param $message
     * @param string $type
     * @return false|string
     */
    public static function sendJsonMessage(string $message, $type = 'success')
    {
        return json_encode([
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * Send a json response
     * @param $data
     * @return false|string
     */
    public static function sendJsonResponse($data)
    {
        return json_encode($data);
    }
}