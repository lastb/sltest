<?php

namespace SLTest\Core\Controllers;


use SLTest\Core\Theme\Theme;
use SLTest\Core\View\Format\HtmlFormat;
use SLTest\Core\View\View;

class PageController
{
    /** @var array массив тегов заголовка. */
    protected $heads = array();

    /** @var array массив js-скриптов страницы. */
    protected $scripts = array();

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
     * Добавляет скрипт в секцию /body
     *
     * @param string $url путь до скрипта.
     */
    protected function addScript($url)
    {
        $this->scripts[] = '<script src="' . $url . '" type="text/javascript"></script>';
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
        $this->prepareRender($vars);

        $page_vars = array(
            'title' => $this->title,
            'heads' => $this->heads,
            'scripts' => $this->scripts,
            'content' => Theme::render($template, $vars),
        );

        return new View(new HtmlFormat(), Theme::render('page', $page_vars));
    }

    /**
     * Подготавливает страницу перед выводом.
     *
     * @param array $vars параметры страницы.
     */
    protected function prepareRender(/** @noinspection PhpUnusedParameterInspection */&$vars)
    {
        $this->addHead('<link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css" >');
        $this->addScript('/assets/js/jquery-3.3.1.min.js');
        $this->addScript('/assets/js/bootstrap.bundle.min.js');
    }
}