<?php

namespace SLTest\Core\View;


use SLTest\Core\Http\Response;
use SLTest\Core\View\Format\FormatInterface;

class View
{
    /** @var array дополнительные заголовки ответа */
    public static $headers = array();

    /** @var FormatInterface формат вывода данных. */
    protected $format;

    /** @var mixed данные для вывода. */
    protected $data;

    /** @var array параметры вывода. */
    protected $options;

    /**
     * View constructor.
     *
     * @param FormatInterface $format формат вывода данных.
     * @param mixed $data параметры для вывода.
     * @param array $options дополнительные данные для вывода.
     */
    public function __construct($format, $data, array $options = [])
    {
        $this->format = $format;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * Возвращает сформированный http-ответ для вывода в браузере.
     *
     * @return Response сформированный http-ответ.
     */
    public function getResponse()
    {
        $this->format->prepare($this->data, $this->options);
        $response = new Response($this->data, Response::HTTP_STATUS_OK, static::$headers);

        return $response;
    }
}