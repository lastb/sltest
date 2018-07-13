<?php

namespace SLTest\App\Controllers;


use SLTest\Core\Controllers\PageController;
use SLTest\Core\Controllers\ListPageTrait;
use SLTest\Core\Database\Database;
use SLTest\Core\Exception\InvalidArgumentException;
use SLTest\Core\Exception\RuntimeException;
use SLTest\Core\Http\Request;
use SLTest\Core\Kernel\Kernel;

class FeedBack extends PageController
{
    use ListPageTrait;

    /** @var Database */
    protected $database;

    /**
     * FeedBack constructor.
     */
    public function __construct()
    {
        $this->database = new Database(Kernel::config());
    }

    /**
     * Загружает список обращений и выводит форму для их отправки.
     *
     * @param int $nav_id id элемента для навигации.
     * @param int $limit лимит списка
     * @param bool $forward направление выборки (вперед/назад)
     * @param string $callback текст сообщения об ошибке или статус успешной отправке обращения.
     * @param Request $request текущий http-запрос
     *
     * @return \SLTest\Core\View\View сформированный контент страницы
     */
    public function list($nav_id, $limit, $forward, $callback, Request $request)
    {
        $this->setTitle('Форма обратной связи.');
        $items = $this->getItems($nav_id, $limit, $forward, 'DESC');
        if ($callback) {
            $callback = $this->decodeCallback($callback);
        }

        return $this->renderPage('feedback/index', $request, [
            'items' => $items,
            'uri' => $request->getUri(),
            'callback' => $callback,
            'pagination' => $this->buildPagination(
                $nav_id,
                $limit,
                $forward,
                $request
            ),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function loadItems($nav_id, $limit, $forward, $order_by)
    {
        $params = [':limit' => $limit];
        $query = 'SELECT * FROM {feedback}';
        if ($nav_id) {
            $query .= ' WHERE id ' . $this->loadItemsCompare($order_by, $forward) . ' :id';
            $params[':id'] = $nav_id;
        }
        $query .= ' ORDER BY id ' . $this->loadItemsOrderBy($order_by, $forward) . ' LIMIT :limit';

        return $this->database->fetchAllAssoc($query, $params);
    }

    /**
     * Добавляет в бд новую запись из формы обратной связи.
     *
     * @param string $name имя пользователя.
     * @param string $email email пользователя.
     * @param string $text текст обращения.
     *
     * @return \SLTest\Core\Http\Response редирект на страницу списка с ошибкой, или с флагом успешной записи.
     */
    public function add($name, $email, $text)
    {
        try {
            if (empty($name) || empty($email) || empty($text)) {
                throw new InvalidArgumentException('Все поля обязательны для заполнения!');
            }

            // @todo: mapping fields?

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Поле email должно содержать только email!');
            }

            $query = 'INSERT INTO {feedback} (name, email, text) VALUES (:name, :email, :text)';
            if (!$this->database->execute($query, [':name' => $name, ':email' => $email, ':text' => $text])) {
                throw new RuntimeException('Не удалось сохранить ваше сообщение.');
            }
        } catch (\Exception $e) {
            return $this->redirect('/feed-back/list?callback=' . $this->encodeCallback($e->getMessage()));
        }

        return $this->redirect('/feed-back/list?callback=' . $this->encodeCallback('success'));
    }

    /**
     * Кодирует callback данные для передачи по url.
     *
     * @param string $callback данные для передачи.
     *
     * @return string закодированные данные.
     */
    protected function encodeCallback($callback)
    {
        return urlencode(base64_encode($callback));
    }

    /**
     * Декодирует данные полученные по url.
     *
     * @param string $callback полученные данные.
     *
     * @return string декодированные данные.
     */
    protected function decodeCallback($callback)
    {
        return strip_tags(base64_decode($callback)); // strip_tags для защиты от xss!
    }
}