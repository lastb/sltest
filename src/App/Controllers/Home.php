<?php

namespace SLTest\App\Controllers;


use SLTest\Core\Controllers\PageController;
use SLTest\Core\Http\Request;

class Home extends PageController
{
    public function index(Request $request)
    {
        $this->setTitle('Текстовое задание для SL');

        return $this->renderPage('index', $request);
    }
}