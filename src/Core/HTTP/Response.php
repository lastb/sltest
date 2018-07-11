<?php

namespace SLTest\Core\Http;

use SLTest\Core\Exception\InvalidArgumentException;


class Response
{
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_NO_CONTENT = 204;
    const HTTP_STATUS_PARTIAL_CONTENT = 206;
    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_UNAUTHORIZED = 401;
    const HTTP_STATUS_FORBIDDEN = 403;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_STATUS_INTERNAL_ERROR = 500;

    /** @var int код http-статуса. */
    protected $status;

    /** @var array список http-заголовков. */
    protected $headers = array();

    /** @var mixed контент http-ответа. */
    protected $body;

    /**
     * Response constructor.
     *
     * @param string $body контент http-ответа.
     * @param int $status код http-статуса.
     * @param array $headers список http-заголовков.
     */
    public function __construct($body = '', $status = self::HTTP_STATUS_OK, $headers = array())
    {
        $this->body = $body;
        $this->status = self::HTTP_STATUS_OK;

        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * Устанавливает значение статуса http-ответа.
     *
     * @param int $code статус.
     */
    public function setStatus($code)
    {
        $this->status = $code;
    }

    /**
     * Устанавливает переданные http-заголовки.
     *
     * @param array $headers массив заголовков, где ключ массива - имя заголовка,
     * а значение массива - параметр заголовка.
     */
    public function setHeaders($headers)
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
    }

    /**
     * Устанавливает http-заголовок.
     *
     * @param string $key имя http-заголовка
     * @param string $value значение http-заголовка.
     */
    public function setHeader($key, $value)
    {
        if (is_numeric($key)) {
            throw new InvalidArgumentException('Не правильное имя заголовка http-ответа.');
        }

        $this->headers[$key] = $value;
    }

    /**
     * Возвращает заголовок content-type
     *
     * @return string|false заголовок content-type или false, если он не указан.
     */
    public function getContentType()
    {
        return $this->headers['Content-Type'] ?? false;
    }

    /**
     * Устанавливает контент http-ответа.
     *
     * @param string $body контент.
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Возвращает код http-статуса.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Возвращает список заголовков http-ответа.
     *
     * @return array список заголовков.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Возвращает контент http-ответа.
     *
     * @return string контент.
     */
    public function getBody()
    {
        return $this->body;
    }
}