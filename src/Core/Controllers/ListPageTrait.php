<?php

namespace SLTest\Core\Controllers;


use SLTest\Core\Http\Request;
use SLTest\Core\Theme\Theme;

/**
 * Trait ListPageTrait
 *
 * @package SLTest\Core\Controllers
 *
 * Трейт для списков.
 */
trait ListPageTrait
{
    /** @var bool|array первый элемент списка, для пагинации  */
    protected $first_item = false;

    /** @var bool|array последний элемент списка, для пагинации  */
    protected $last_item = false;

    /**
     * Получает элементы списка а так же запрашивает +1 и -1 элемент для формирования пагинации.
     * Пагинация реализована через сравнения с primary key, без использования offset.
     *
     * @param int $nav_id id элемента для навигации.
     * @param int $limit лимит списка
     * @param bool $forward направление выборки (вперед/назад)
     * @param string $order_by сортировка элементов в выборке.
     *
     * @return array список элементов.
     */
    protected function getItems($nav_id, $limit, $forward = false, $order_by = 'ASC')
    {
        $items = $this->loadItems($nav_id, $limit + 1, $forward, $order_by);
        if (count($items) > $limit) {
            array_pop($items);
            $this->last_item = $items[count($items)-1];
        }

        if ($nav_id > 0) {
            if (($prev_items = $this->loadItems($nav_id, 1, !$forward, $order_by)) !== false) {
                $this->first_item = $items[0];
            }
        }

        if (!$forward) {
            $items = array_reverse($items);
        }

        return $items;
    }

    /**
     * Загружает элементы списка.
     *
     * @param int $nav_id id элемента для навигации.
     * @param int $limit лимит списка
     * @param bool $forward направление выборки (вперед/назад)
     * @param string $order_by сортировка элементов в выборке.
     *
     * @return array список загруженных элементов.
     */
    abstract protected function loadItems($nav_id, $limit, $forward, $order_by);

    /**
     * Определяет как нужно сортировать элементы при выборке, для правильной пагинации.
     *
     * @param string $order_by сортировка элементов в выборке.
     * @param bool $forward направление выборки (вперед/назад)
     *
     * @return string направление сортировки.
     */
    protected function loadItemsOrderBy($order_by, $forward)
    {
        if (!$forward) {
            $order_by = ($order_by == 'DESC') ? 'ASC' : 'DESC';
        }

        return $order_by;
    }

    /**
     * Определяет сравнение элементов при выборке, для правильной пагинации.
     *
     * @param string $order_by сортировка элементов в выборке.
     * @param bool $forward направление выборки (вперед/назад)
     *
     * @return string знак сравнения.
     */
    protected function loadItemsCompare($order_by, $forward)
    {
        return ($forward && $order_by == 'ASC' || !$forward && $order_by == 'DESC') ? '>' : '<';
    }

    /**
     * Формирует шаблон навигации и возвращает его.
     *
     * @param int $nav_id id элемента для навигации.
     * @param int $limit лимит списка
     * @param bool $forward направление выборки (вперед/назад)
     * @param Request $request http-запрос.
     *
     * @return bool|string контент шаблона навигации, или false если навигации нет в списке.
     */
    protected function buildPagination($nav_id, $limit, $forward, Request $request)
    {
        $output = false;
        if ($nav_id > 0 || $this->last_item) {
            $output = Theme::render('pagination', array(
                'uri_path' => parse_url($request->getUri(), PHP_URL_PATH),
                'nav_id' => $nav_id,
                'prev_id' => $this->getPaginationPrevId($forward ? $this->first_item : $this->last_item),
                'next_id' => $this->getPaginationNextId($forward ? $this->last_item : $this->first_item),
                'limit' => $limit,
            ));
        }

        return $output;
    }

    /**
     * Получает nav_id предыдущего элемента списка, для формирования правильной пагинации.
     *
     * @param array $item первый элемент списка.
     *
     * @return bool|mixed nav_id элемента или false - если элемента нет.
     */
    protected function getPaginationPrevId($item)
    {
        return $item ? $item['id']: false;
    }

    /**
     * Получает nav_id следующего элемента списка, для формирования правильной пагинации.
     *
     * @param array $item последний элемент списка.
     *
     * @return bool|mixed nav_id элемента или false - если элемента нет.
     */
    protected function getPaginationNextId($item)
    {
        return $item ? $item['id'] : false;
    }
}