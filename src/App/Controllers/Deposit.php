<?php

namespace SLTest\App\Controllers;


use SLTest\Core\Controllers\PageController;
use SLTest\Core\Http\Request;

class Deposit extends PageController
{
    public function list(Request $request)
    {
        $this->setTitle('Сортировка таблицы на Javascript');
        $this->addScript('/assets/js/slt.js', 'module');

        return $this->renderPage('deposit/list', $request);
    }
}