<?php

namespace SLTest\Core\View\Format;


interface FormatInterface
{
    /**
     * Выполняет подготовку перед выводом данных.
     *
     * @param mixed $data передаваемые данные.
     * @param array $options парамтеры обработки.
     */
    public function prepare(&$data, $options);
}