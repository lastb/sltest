<?php

namespace SLTest\App\Controllers;


use SLTest\Core\Controllers\PageController;

class Home extends PageController
{
    public function index()
    {
        $this->setTitle('Текстовое задание для SL');

        return $this->renderPage('index');
    }
}