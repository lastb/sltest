<?php

namespace SLTest\Core\Database\Exception;


class QueryException extends RuntimeException
{
    /** @var string текст запроса. */
    protected $query;

    /**
     * QueryException constructor.
     *
     * @param string $message
     * @param string $query
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $query, $code = 0, \Exception $previous = null)
    {
        $this->query = $query;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Возвращает текст sql запроса.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}