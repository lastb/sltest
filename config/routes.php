<?php

return array(
  '@data' => array(
      '/' => array(
          'controller' => 'SLTest\App\Controllers\Home::index'
      ),
      '/deposit/list' => array(
          'method' => 'GET',
          'controller' => 'SLTest\App\Controllers\deposit::list'
      ),
      '/feed-back/list' => array(
          'method' => 'GET',
          'args' => array(
              'nav_id' => ['type' => 'int', 'default' => 0],
              'limit' => ['type' => 'int', 'default' => 5],
              'forward' => ['type' => 'int', 'default' => 1],
              'callback' => ['type' => 'string'],
          ),
          'controller' => 'SLTest\App\Controllers\FeedBack::list'
      ),
      '/feed-back/add' => array(
          'method' => 'POST',
          'args' => array(
              'name' => ['type' => 'string', 'required' => true],
              'email' => ['type' => 'string', 'required' => true],
              'text' => ['type' => 'string', 'required' => true],
          ),
          'controller' => 'SLTest\App\Controllers\FeedBack::add'
      ),
  ),
);