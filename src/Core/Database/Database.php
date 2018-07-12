<?php

namespace SLTest\Core\Database;


use SLTest\Core\Database\Exception\ConnectionException;
use SLTest\Core\Database\Exception\QueryException;

class Database
{
    /** @var array настройки . */
    protected $config;

    /** @var \PDO экземпляр пдо объекта */
    protected $pdo;

    /**
     * Подключение к бд.
     *
     * @throws ConnectionException
     */
    public function connect()
    {
        try {
            if (empty($this->pdo)) {
                $conf = $this->config['database'];
                $this->pdo = new \PDO("{$conf['driver']}:dbname={$conf['db']};host={$conf['host']};port={$conf['port']}", $conf['user'], $conf['password']);

                if (!empty($this->config['pdo']['attributes'])) {
                    foreach ($this->config['pdo']['attributes'] as $key => $value) {
                        $key = mb_strtoupper($key);
                        $value = mb_strtoupper($value);
                        $this->pdo->setAttribute(constant("\PDO::{$key}"), constant("\PDO::{$value}"));
                    }
                }
            }

        } catch (\Exception $e) {
            throw new ConnectionException('Ошибка подключения к бд', 0, $e);
        }
    }

    /**
     * Подготавливает запрос для дальнешей обработки.
     *
     * @param string $query текст запроса.
     * @param array $params список параметров запроса.
     *
     * @return \PDOStatement
     */
    protected function prepareQuery($query, array &$params)
    {
        $this->replacePrefix($query);

        // ищем параметеры которые являются массивом, и создаем из них список параметров.
        $replace = array();
        foreach ($params as $key => $param) {
            if (is_array($param)) {
                // перобраузем пармаетр в несколько параметров.
                $replace[$key] = array();
                $values = array_values($param);

                for ($i = 0; $i < count($values); $i++) {
                    $index = $key . '_' . $i;

                    $replace[$key][] = $index;

                    // записываем значение в список параметров.
                    $params[$index] = $values[$i];
                }

                $replace[$key] = implode(', ', $replace[$key]);

                // сам параметр удаляем.
                unset($params[$key]);
            }
        }

        $query = strtr($query, $replace);
        $sth = $this->pdo->prepare($query);

        return $sth;
    }

    /**
     * Добавляет префикс к таблице в запросе.
     *
     * @param string $query sql-запрос.
     */
    protected function replacePrefix(&$query)
    {
        $prefix = $this->config['database']['prefix'] ?? '';
        if (preg_match_all('#\{(.*?)\}#us', $query, $matches, PREG_SET_ORDER) > 0) {
            $replace = array();

            foreach ($matches as $match) {
                $replace[$match[0]] = $prefix ? $prefix . '_' . $match[1] : $match[1];
            }
            $query = strtr($query, $replace);
        }
    }

    /**
     * Выполняет select запрос к бд.
     *
     * @param string $query текст запроса.
     * @param array $params параметры запроса.
     *
     * @return \PDOStatement
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    protected function select($query, array $params)
    {
        $this->connect();

        try {
            $sth = $this->prepareQuery($query, $params);
            $sth->execute($params);
        } catch (\PDOException $e) {
            throw new QueryException('Извините, произошла ошибка в базе данных.', (!empty($sth) ? $sth->queryString : $query), 0, $e);
        }

        return $sth;
    }

    /**
     * Database constructor.
     *
     * @param array $config настройки
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Выполняет execute запрос к бд. (insert/update/delete)
     *
     * @param string $query текст запроса.
     * @param array $params параметры запроса.
     * @param boolean $rows_return флаг, если true - возвращает колличество измененных записей, иначе возвращает успешность запроса.
     *
     * @return bool|int true если запрос выполене успешно, иначе false.
     * Возвращает количество обновленных записей если row_return = true.
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    public function execute($query, array $params  = array(), $rows_return = false)
    {
        try {
            $this->connect();
            $sth = $this->prepareQuery($query, $params);
            $result = $sth->execute($params);
        } catch (\PDOException $e) {
            throw new QueryException('Извините, произошла ошибка в базе данных.', (!empty($sth) ? $sth->queryString : $query), 0, $e);
        }

        return $rows_return ? $sth->rowCount() : $result;
    }

    /**
     * Выполняет запрос к бд.
     *
     * @param string $query sql-запрос.
     */
    public function sqlQuery($query)
    {
        $this->replacePrefix($query);

        $this->pdo->exec($query);
    }

    /**
     * Выполняет select к бд и возвращает выборку в виде массива.
     *
     * @param string $query текст запроса.
     * @param array $params параметры запроса.
     *
     * @return array возвращает массив строк, где каждая строка - это ассоциативный массив.
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    public function fetchAllAssoc($query, array $params = array())
    {
        return $this->select($query, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Выполняет select к бд и возвращает выборку в виде массива.
     *
     * @param string $query текст запроса.
     * @param string $key имя поля.
     * @param array $params параметры запроса.
     *
     * @return array возвращает массив строк, где каждая строка - это ассоциативный массив.
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    public function fetchAllKeyed($query, $key, array $params = array())
    {
        $sth = $this->select($query, $params);

        $result = array();
        while($row = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $result[$row[$key]] = $row;
        }

        return $result;
    }

    /**
     * Выполняет select к бд и возвращает выборку в виде массива.
     *
     * @param string $query текст запроса.
     * @param array $params параметры запроса.
     * @param int $index колонка массива.
     *
     * @return array возвращает колонку представленную в виде массива.
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    public function fetchCol($query, array $params = array(), $index = 0)
    {
        return $this->select($query, $params)->fetchAll(\PDO::FETCH_COLUMN, $index);
    }

    /**
     * Выполняет select к бд и возвращает выборку в виде массива ключ => значение.
     *
     * @param string $query текст запроса.
     * @param array $params параметры запроса.
     *
     * @return array возвращает массив где ключ первое поле запроса, а значение второе поле запроса.
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    public function fetchKeyed($query, array $params = array())
    {
        return $this->select($query, $params)->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * Выполняет select к бд и возвращает выборку в виде массив.
     *
     * @param string $query текст запроса.
     * @param array $params параметры запроса.
     *
     * @return array|false возвращает первую строку в виде массива, или false в случае неудачи.
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    public function fetch($query, array $params = array())
    {
        return $this->select($query, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Выполняет select к бд и возвращает значение поля первой строки.
     *
     * @param string $query текст запроса.
     * @param array $params параметры запроса.
     * @param int $index номер поля, значение которго будет выведено.
     *
     * @return mixed значение указаного поля из первой строки.
     *
     * @throws QueryException
     * @throws ConnectionException
     */
    public function fetchColumn($query, array $params = array(), $index = 0)
    {
        return $this->select($query, $params)->fetchColumn($index);
    }
}