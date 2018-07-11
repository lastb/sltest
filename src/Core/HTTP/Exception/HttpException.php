<?php

namespace SLTest\Core\HTTP\Exception;


use SLTest\Core\Exception\Exception;
use Throwable;

class HttpException extends Exception
{
    /** @var int http-статус код ошибки. */
    private $http_status_code;

    /** @var array  http-заголовки ошибки. */
    private $http_headers;

    /**
     * HttpException constructor.
     *
     * @param int $http_status_code
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param array $http_headers
     */
    public function __construct(int $http_status_code, string $message = "", int $code = 0, Throwable $previous = null, array $http_headers = [])
    {
        $this->http_status_code = $http_status_code;
        $this->http_headers = $http_headers;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Возвращает http-статус код ошибки.
     *
     * @return int http-статус код .
     */
    public function getHttpStatusCode()
    {
        return $this->http_status_code;
    }

    /**
     * Возвращает список заголовков http.
     *
     * @return array список заголовков http.
     */
    public function getHttpHeaders()
    {
        return $this->http_headers;
    }
}