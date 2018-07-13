<?php

namespace SLTest\App\Controllers;


use SLTest\Core\Controllers\PageController;
use SLTest\Core\Exception\RuntimeException;
use SLTest\Core\Generator\Helper;
use SLTest\Core\Http\Request;
use SLTest\Core\Reader\LargeFileReader;

class Tests extends PageController
{
    const DEFAULT_ITERATION_COUNT = 10000;
    const DEFAULT_MAX_LINE = 26037744;
    const LARGE_FILE_PATH = STORAGE_DIR . '/large-file.txt'; // @todo: move to generator helper ??

    /**
     * Генерирует большой файл и возвращает управление на страницу с тестами.
     *
     * @return \SLTest\Core\Http\Response
     */
    public function largeFileGenerate()
    {
        ob_start();
        Helper::generateLargeFile(ASSETS_DIR . '/generator/words.txt', self::LARGE_FILE_PATH);
        ob_end_clean();

        return $this->redirect('/tests/large-file');
    }

    /**
     * Запуск тестов риадера и формы для их запуска.
     *
     * @param string $test имя теста
     * @param array $test_options параметры тестов.
     * @param Request $request http-запросы.
     *
     * @return \SLTest\Core\View\View
     */
    public function largeFile($test, $test_options, Request $request)
    {
        $this->setTitle('Тест чтения файла 2GB');
        $exists = file_exists(self::LARGE_FILE_PATH);

        $data = array('type' => $test);
        if ($test == 'iteration') { // запуск текста
            if (!$exists) {
                throw new RuntimeException('Для запуска теста, необходимо сгенерировать файл!');
            }

            $reader = new LargeFileReader(self::LARGE_FILE_PATH);
            $data += $this->runTest(function () use ($reader, $test_options) {
                $test_options['iteration_count'] = $test_options['iteration-count'] ?? self::DEFAULT_ITERATION_COUNT;
                $real_max_line = 0;
                $test_options['max-line'] = $test_options['max-line'] ?? self::DEFAULT_MAX_LINE;

                for ($i = 0; $i < $test_options['iteration-count']; $i++) {
                    $r = rand(0, $test_options['max-line']);
                    $real_max_line = max($r, $real_max_line);
                    $reader->seek($r);
                }

                return array(
                    'iteration_count' => $test_options['iteration-count'],
                    'max_line' => $test_options['max-line'],
                    'real_max_line' => $real_max_line,
                );
            });
        } elseif ($test == 'position') {
            $reader = new LargeFileReader(self::LARGE_FILE_PATH);
            $position = $test_options['position'] ?? 0;

            $data += $this->runTest(function() use ($position, $reader) {
                $reader->seek($position);

                return array(
                    'position' => $position,
                    'line' => $reader->current(),
                );
            });
        }

        return $this->renderPage('tests/large-file', $request, array(
            'has_file' => $exists,
            'test' => $data,
        ));
    }

    /**
     * Обертка для запуска теста.
     *
     * @param \Closure $callback
     *
     * @return array|mixed
     */
    private function runTest(\Closure $callback)
    {
        $micro = microtime(true);
        $memory_usage = memory_get_usage();
        $error = '';
        $data = array();

        try {
            $data = $callback();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

         $data += array(
            'micro_time' => sprintf('%.8f', (microtime(true) - $micro)),
            'memory_usage' => (memory_get_usage() - $memory_usage) / 1024 / 1024,
            'error' => $error,
        );

        return $data;
    }
}