<?php

namespace SLTest\Core\Http;

class Request
{
    /** Список http - методов */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /** @var string метод запроса. */
    protected $method;

    /** @var string uri запроса.*/
    protected $uri;

    /** @var string тело запроса. */
    protected $body;

    /** @var array список заголовков запроса. */
    public $headers = array();

    /** @var array список параметров переданных через GET. */
    public $query = array();

    /** @var array список параметров переданных через POST. */
    public $request = array();

    /** @var array список файлов переданных в запросе. */
    public $files = array();

    /** @var array список значений cookies. */
    public $cookies = array();

    /**
     * Request constructor.
     *
     * @param string $uri адрес на который был отправле запрос.
     * @param array $query GET - параметры запроса.
     * @param array $request POST - параметры запроса.
     * @param array $cookies кукисы запроса.
     * @param array $files файлы запроса.
     * @param string $body тело запроса.
     * @param array $headers заголовки запроса.
     */
    public function __construct($uri, $query = array(), $request = array(), $cookies = array(), $files = array(), $body = '', array $headers = array())
    {
        $this->uri = $uri;
        $this->query = $query;
        $this->request = $request;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->body = $body;

        $this->setHeaders($headers);
    }

    /**
     * Возвращает метод запроса.
     *
     * @return null|string если метод поддерживается возвращает имя метода, иначе null;
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Возвращает uri запроса.
     *
     * @return string uri запроса.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Возвращает часть от пути.
     *
     * @param int $component @see parse_url $component
     *
     * @return array|string
     */
    public function getUriPart($component = -1)
    {
        return parse_url($this->uri, $component);
    }

    /**
     * Получает значение у параметра запроса.
     *
     * @param string $key идентификатор параметра
     * @param mixed $default значение по умолчанию, если параметра не существует.
     * @return mixed возвращает значение параметра, или, если если он не существует, значение по умолчанию.
     */
    public function get($key, $default = null)
    {
        if (isset($this->query[$key])) {
            return $this->query[$key];
        } elseif (isset($this->request[$key])) {
            return $this->request[$key];
        } else {
            return $default;
        }
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
     * Сохраняет значение заголовка.
     *
     * @param string $header название заголовка.
     * @param string $value значение заголовка.
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
    }

    /**
     * Получает значение заголовка.
     *
     * @param string $key имя заголовка.
     * @param mixed $default значение по умолчанию, если заголовок не указан.
     * @return mixed возвращает или значение заголовка, или значение по умолчанию, если заголовок не установлен.
     */
    public function getHeader($key, $default = null)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }

    /**
     * Возвращает все заголовки запроса.
     *
     * @return array|bool список заголовков или false если их нет.
     */
    public function getHeaders()
    {
        return !empty($this->headers) ? $this->headers : false;
    }

    /**
     * Получает значение переменной из куки.
     *
     * @param string $key идентификатор куки.
     * @param mixed $default значение по умолчанию, если кука не задана.
     * @return mixed возвращает или значение куки, или значение по умолчанию, если кука не установлена.
     */
    public function getCookie($key, $default = null)
    {
        return isset($this->cookies[$key]) ? $this->cookies[$key] : $default;
    }
}