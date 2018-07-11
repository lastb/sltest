<?php

namespace SLTest\Core\Config;


use SLTest\Core\Exception\LogicException;

class ConfigLoader
{
    /**
     * Загружает конфигурацию из файла.
     *
     * @param string $filename имя файла конфигурации для загрузки.
     *
     * @return mixed загруженные данные конфигурации.
     */
    public function load($filename)
    {
        $config = $this->loadFile($filename);
        foreach ($config as $key => $value) {
            if ($key == '@include') {
                $config['@data'] += $this->load($value);
            }
        }

        return $config['@data'];
    }

    /**
     * Выполняет загрузку данныех из файла конфигурации.
     *
     * @param string $filename имя файла конфигурации для загрузки.
     *
     * @return mixed загруженные данные файла.
     */
    private function loadFile($filename)
    {
        $path = CONFIG_DIR . '/' . $filename;
        if (!file_exists($path)) {
            throw new LogicException(sprintf('Не найден файл конфигурации "%s".', $path));
        }
        $config = include $path;
        if (!is_array($config)) {
            throw new LogicException(sprintf('Файл конфигурации "%s" должен возвращать массив!', $path));
        }

        return $config;
    }
}