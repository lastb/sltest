<?php

namespace SLTest\App\Controllers;


use SLTest\Core\Http\Response;

class Home
{
    public function index()
    {

        return new Response('index page');
    }
}