<?php

namespace SLTest\Core\Generator;


class Helper
{
    /**
     * Генерирует большой текстовый файл на основе словаря.
     *
     * @param string $words_file_path путь к словарю.
     * @param string $dst место сохранения сгенерированного файла.
     * @param int $line_limit максимальное количество символов в линии.
     * @param int $chunk_size максимальный размер блока.
     * @param int $max_chunk_amount максимальное количество блоков.
     */
    static public function generateLargeFile($words_file_path, $dst, $line_limit = 80, $chunk_size = 32768, $max_chunk_amount = 65536)
    {
        $words = file($words_file_path);

        $content = "";
        do {
            $line = "";
            do {
                $line .= trim($words[array_rand($words)]) . " ";
            } while(strlen($line) < $line_limit);
            $content .= trim($line) . "\n";
        } while(strlen($content) < $chunk_size);

        $handle = fopen($dst, "w");
        $chunk_amount = 0;
        do {
            fwrite($handle, $content);
            $chunk_amount++;
            echo $chunk_amount . "\n";
        } while($chunk_amount < $max_chunk_amount);
        fclose($handle);
    }

}