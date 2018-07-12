<?php

namespace SLTest\App\Controllers;


use SLTest\Core\Controllers\PageController;

class Deposit extends PageController
{
    public function list()
    {
        $this->setTitle('Сортировка таблицы на Javascript');
        $this->addScript('/assets/js/slt.js', 'module');

        return $this->renderPage('deposit/list');
    }
}