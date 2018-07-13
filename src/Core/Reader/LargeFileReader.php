<?php

namespace SLTest\Core\Reader;


use SLTest\Core\Exception\InvalidArgumentException;
use SLTest\Core\Exception\LogicException;
use SLTest\Core\Exception\OutOfBoundsException;

/**
 * Class LargeFileReader
 * @package SLTest\Core\Reader
 *
 * Разбивает большой файл на мелкие кусочки, и сохранят номер максимальной строки в кусочке.
 * Это позволят не тратя много памяти ~ 20mb быстро перемещаться по файлу в разных направлениях.
 */
class LargeFileReader implements \SeekableIterator
{
    /** @var int размер кусочка. */
    private $chunk_size = 8192;

    /** @var int текущая позиция (номер строки). */
    private $position = 0;

    /** @var int текущий кусок в котором находится текущая строка. */
    private $cur_chunk = 0;

    /** @var array все сохраненные куски. */
    private $chunks = array();

    /** @var resource указатель на файл. */
    private $handle;

    /** @var int размер файла. */
    private $file_size;

    /**
     * LargeFileReader constructor.
     *
     * @param string $filename имя файла для прохода.
     */
    public function __construct($filename)
    {
        if (($handle = fopen($filename, 'r')) === false) {
            throw new InvalidArgumentException(sprintf('Файл "%s" не найден.', $filename));
        }

        $this->handle = $handle;
        $this->file_size = filesize($filename);
        $this->storeChunks(0);
    }

    /**
     * LargeFileDestructor
     */
    public function __destruct()
    {
        if (!empty($this->handler)) {
            fclose($this->handler);
        }
    }

    /**
     * @inheritdoc
     */
    public function seek($position) {
        if (($chunk_id = $this->storeChunks($position)) === false || $position > $this->chunks[$chunk_id]) {
            throw new OutOfBoundsException("Недействительная позиция ($position)");
        }

        $this->position = $position;
        $this->cur_chunk = $chunk_id;
    }

    /**
     * Кэширует номера строк файла по кусочка, если кеш уже выполнен, возвращает номер кусочка,
     * или false если он не найден.
     *
     * @param int $position номер искомой строки.
     *
     * @return bool|int номер кусочка или false если номер строки не найден.
     */
    private function storeChunks($position)
    {
        // если позиция уже закеширована - просто выберем её.
        $chunk_count = count($this->chunks);
        if (!empty($this->chunks) && $this->chunks[$chunk_count - 1] >= $position) {

            return $this->findChunkId($position, 0, $chunk_count);
        }

        // перемещаем указатель файла на тот массив данных, которых нет в кеше.
        $offset = count($this->chunks) * $this->chunk_size;
        fseek($this->handle, $offset);

        $chunk_id = false;
        $count = 0;
        do {
            // читаем новый кусочек
            if (($line = fread($this->handle, $this->chunk_size)) === false) {
                break;
            }

            // для точного определения номера строк в кусочке, смотрим в предыдущий кусочек или
            // 0, если это первый кусок.
            if ($chunk_id = count($this->chunks)) {
                $count = $this->chunks[$chunk_id - 1];
            }
            // получаем количество строк в новом кусочке и максимальный номер строки в текущем куске.
            $count += substr_count($line, "\n");
            // строка может быть между двумя кусками, по этому нужно проверь является ли последняя строка законченной,
            // если это не так - не считаем её.
            if ($line[$this->chunk_size-1] != "\n") {
                $count--;
            }
            // записываем максимальный номер строки в кусок.
            $this->chunks[$chunk_id] = $count;

            // читаем пока не дошли до конца файла, или до нужного номера строки.
            $offset += $this->chunk_size;
        } while ($offset < $this->file_size && $position > $count);

        return $chunk_id;
    }

    /**
     * Выполняет рекурсивный поиск ид кусочка по номеру строки от $first-id кусочка до $last-id.
     *
     * @param int $position номер строки
     * @param int $first от какого ид начинать поиск
     * @param int $last до какого ид выполняется поиск.
     *
     * @return int номер ид кусочка.
     */
    private function findChunkId($position, $first, $last)
    {
        $chunk_id = (int)floor(($first + $last) / 2);
        if ($this->isPositionInChunkId($position, $chunk_id)) {

            return $chunk_id;
        } elseif ($this->isPositionInChunkId($position, $first)) {

            return $first;
        } elseif ($this->isPositionInChunkId($position, $last)) {

            return $last;
        } else {
            if ($this->chunks[$chunk_id] - $position >= 0) {
                $last = $chunk_id;
            } else {
                $first = $chunk_id;
            }

            return $this->findChunkId($position, $first, $last);
        }
    }

    /**
     * Проверят находится ли указанный номер строки в переданном кусочке.
     *
     * @param int $position номер строки
     * @param int $chunk_id кусочек в котором ищется номер строки.
     *
     * @return bool true если номер строки находится в указанном кусочке, иначе false.
     */
    private function isPositionInChunkId($position, $chunk_id)
    {
        $index = $this->chunks[$chunk_id] - $position;

        return $index >= 0 && $index <= $this->getChunkLineCount($chunk_id);
    }

    /**
     * Возвращает количество строк в кусочке.
     *
     * @param int $chunk_id id кусочка.
     *
     * @return int количество строк.
     */
    private function getChunkLineCount($chunk_id)
    {
        $count = $this->chunks[$chunk_id];
        if ($chunk_id > 0) {
            $count = $count - $this->chunks[$chunk_id - 1] - 1;
        }

        return $count;
    }

    /**
     * @inheritdoc
     */
    public function rewind() {
        $this->position = 0;
        $this->cur_chunk = 0;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return $this->getLineInChunk($this->position, $this->cur_chunk);
    }

    /**
     * Читает строку по её номеру из указанного кусочка.
     *
     * @param int $position номер строки для чтения.
     * @param int $chunk_id номер кусочка в котором должна находится строка.
     *
     * @return string строка по указанному номеру.
     */
    private function getLineInChunk($position, $chunk_id)
    {
        if (empty($this->chunks[$chunk_id])) {
            throw new LogicException('Неправильный chunk_id.');
        }

        $chunk_count = $this->getChunkLineCount($chunk_id);
        // позиция строки в кусочке.
        $index = $chunk_count  - ($this->chunks[$chunk_id] - $position);
        if ($index < 0 || $index > $chunk_count) {
            throw new LogicException('Поиск номера строки в неправильном chunk_id');
        }

        $line = '';
        $data = $this->getChunkData($chunk_id);  // все строки кусочка
        if ($data) {
            $i = 0;
            $stage = $index;
            // перебираем все строки пока не найдем нужную.
            while (($j = strpos($data, "\n", $i)) !== false) {
                if ($stage == 0) {
                    $line = substr($data, $i, $j - $i);
                    break;
                }

                $i = $j+1;
                $stage--;
            }
        }

        // если номер строки первый, а ид кусочек не первый, часть строки может находится в предыдущем кусочке.
        if ($index === 0 && $chunk_id > 0) {
            $prev_data = $this->getChunkData($chunk_id - 1); // данные предыдущего кусочка.
            $prev_index = strrpos($prev_data, "\n", -1); // находим начало последней строки.
            if ($prev_index != $this->chunk_size - 1) { // если это не последний символ предыдущего кусочка.
                $line = substr($prev_data, $prev_index + 1) . $line; // дополняем строку.
            }
        }

        return $line;
    }

    /**
     * Читает данные кусочка.
     *
     * @param int $chunk_id id кусочка.
     *
     * @return bool|string данные кусочка или false в случае ошибки.
     */
    private function getChunkData($chunk_id)
    {
        $offset = $chunk_id * $this->chunk_size;
        fseek($this->handle, $offset);

        return fread($this->handle, $this->chunk_size);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $this->cur_chunk = $this->storeChunks($this->position);

        return $this->cur_chunk !== false && $this->cur_chunk >= $this->position;
    }
}