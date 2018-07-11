<?php

namespace SLTest\Core\Kernel;


use SLTest\Core\Config\ConfigLoader;
use SLTest\Core\Error\ErrorHandler;
use SLTest\Core\Http\Request;
use SLTest\Core\Http\Response;
use SLTest\Core\Router\Router;

class Kernel
{
    /** @var string текущее окружение приложения. */
    private $env;

    /** @var array конфигурация приложения */
    private $config;

    /**
     * Создает ядро, инициализирует, и возвращает его экземпляр. Singletone!
     *
     * @param string $env окружение приложения.
     *
     * @return Kernel экземпляр ядра.
     */
    public static function init($env)
    {
        static $instance = null;
        if (!$instance) {
            $instance = new static($env);
        }

        return $instance;
    }

    /**
     * Kernel constructor.
     * @param string $env окружение приложения.
     */
    public function __construct($env)
    {
        $this->env = $env;
        $loader = new ConfigLoader();

        // загрузка конфигурации.
        $this->config = $loader->load("config.{$env}.php");

        // инициализация ошибок.
        if (!empty($this->config['errors']['error_reporting'])) {
            error_reporting($this->config['errors']['error_reporting']);
        }
        if (!empty($this->config['errors']['display_errors'])) {
            ini_set('display_errors', $this->config['errors']['display_errors']);
        }
        $error_handler = new ErrorHandler();
        set_exception_handler([$error_handler, 'exceptionHandler']);
    }

    /**
     * Обрабатывает http-запрос и возвразает http-ответ.
     *
     * @param Request $request http-запрос.
     *
     * @return Response http-ответ.
     *
     * @throws \SLTest\Core\HTTP\Exception\HttpException
     */
    public function handleRequest(Request $request)
    {
        $router = new Router();
        $data = $router->handleRequest($request);
        if ($data instanceof Response) {
            $response = $data;
        } else {
            $response = new Response($data);
        }

        return $response;
    }
}