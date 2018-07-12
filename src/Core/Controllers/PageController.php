<?php

namespace SLTest\Core\Controllers;


use SLTest\Core\Theme\Theme;
use SLTest\Core\View\Format\HtmlFormat;
use SLTest\Core\View\View;

class PageController
{
    /** @var array массив тегов заголовка. */
    protected $heads = array();

    /** @var string имя станицы. */
    protected $title;

    /**
     * Добавляет тэг в секцию head страницы.
     *
     * @param string $tag добавляемый тэг.
     */
    protected function addHead($tag)
    {
        $this->heads[] = $tag;
    }

    /**
     * Устанавливает заголовк страницы.
     *
     * @param string $title заголовок страницы.
     */
    protected function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Выполняет рендер страницы.
     *
     * @param string $template шаблон для рендера.
     * @param array $vars параметры шалобна.
     *
     * @return \SLTest\Core\View\View клас вывода.
     */
    protected function renderPage($template, array $vars = [])
    {
        $page_vars = array(
            'title' => $this->title,
            'heads' => $this->heads,
            'content' => Theme::render($template, $vars),
        );

        return new View(new HtmlFormat(), Theme::render('page', $page_vars));
    }
}