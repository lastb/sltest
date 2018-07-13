<?php

namespace SLTest\Core\Router;


use SLTest\Core\Config\ConfigLoader;
use SLTest\Core\Exception\RuntimeException;
use SLTest\Core\HTTP\Exception\HttpException;
use SLTest\Core\Http\Request;
use SLTest\Core\Http\Response;

class Router
{
    /** @var array список доступных маршрутов. */
    private $routes;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $loader = new ConfigLoader();
        $this->routes = $loader->load('routes.php');
    }

    /**
     * Пытается выполнить перехват запроса используя доступные маршруты, и возвращает результат обработки
     * перехваченного маршрута.
     *
     * @param Request $request http-запрос.
     *
     * @return mixed рузультат обработки роутера.
     *
     * @throws HttpException
     */
    public function handleRequest(Request $request)
    {
        $path = parse_url($request->getUri(), PHP_URL_PATH);
        if (empty($this->routes[$path])) {
            throw new HttpException(Response::HTTP_STATUS_NOT_FOUND, 'Маршрут не найден');
        }

        $route = $this->routes[$path];
        $route['path'] = $path;
        $this->validateRoute($route, $request);
        $args = !empty($route['args']) ? $this->getRouteArgs($route['args'], $request) : [];
        $args[] = $request;

        return $this->handleRoute($route, $args);
    }

    /**
     * Парсит запрос и возвращает аргументы маршрута взятые из запроса.
     *
     * @param array $args_list список аргументов маршрута с их настройками.
     * @param Request $request http-запрос.
     *
     * @return array аргументы маршрута с их значениями.
     *
     * @throws HttpException
     */
    protected function getRouteArgs($args_list, Request $request)
    {
        $args = array();
        foreach ($args_list as $arg_name => $options){
            $value = $request->get($arg_name);
            if (isset($value)) {
                switch ($options['type']) {
                    case 'numeric':
                        $valid = is_numeric($value);
                        break;

                    case 'string':
                        $valid = is_string($value);
                        break;

                    default:
                        $valid = false;
                }

                if (!$valid) {
                    throw new HttpException(Response::HTTP_STATUS_BAD_REQUEST, sprintf('Неправильный тип параметра "%s".', $arg_name));
                }
            } elseif (isset($options['default'])) {
                $value = $options['default'];
            }

            $args[$arg_name] = $value;
        }

        // @todo: file://input get data

        return $args;
    }

    /**
     * Выполняет предворительную проверку маршрута.
     *
     * @param array $route параметры маршрута.
     * @param Request $request http-запрос.
     *
     * @throws HttpException
     */
    protected function validateRoute($route, Request $request)
    {
        if (isset($route['method']) && $route['method'] != $request->getMethod()) {
            throw new HttpException(Response::HTTP_STATUS_BAD_REQUEST, sprintf('Недопустимый метод "%s" для маршрута "%s".', $route['method'], $route['path']));
        }

        // other route validate like permissions
    }

    /**
     * Создает экземпляр контроллера и вызывает его метод к которому привязан обрабатываемый маршрут.
     *
     * @param array $route параметры маршрута
     * @param array $args аргументы маршрута
     *
     * @return mixed результат выполнения метода контроллера.
     */
    protected function handleRoute($route, $args)
    {
        list($class, $method) = explode('::', $route['controller']);

        try {
            $instance = new $class();
            $method = new \ReflectionMethod($class, $method);
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf('Не удалось создать контроллер "%s" при обработке маршрута "%s".', $class, $route['path']), 0, $e);
        }

        return $method->invokeArgs($instance, $args);
    }
}