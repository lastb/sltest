<?php

namespace SLTest\Core\HTTP;


class Helper
{
    /**
     * Создает HTTP-запрос из глобальных переменных.
     *
     * @return Request HTTP-запрос
     */
    public static function createRequestFromGlobals()
    {
        $request = new Request($_SERVER['REQUEST_URI'], $_GET, $_POST, $_COOKIE, $_FILES, 'php://input');

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = substr($key, 5);

                $request->setHeader($header, $value);
            }
        }
        if (!empty($_SERVER['CONTENT_TYPE'])) {
            $request->setHeader('content_type', $_SERVER['CONTENT_TYPE']);
        }

        return $request;
    }

    /**
     * Выполняет отправку http-ответа в главный буфер.
     *
     * @param Response $response http-ответ.
     */
    public static function sendResponse(Response $response)
    {
        http_response_code($response->getStatus());

        foreach ($response->getHeaders() as $key => $header) {
            header($key . ': ' . $header);
        }

        if (!empty($response->getBody())) {
            print $response->getBody();
        }
    }
}