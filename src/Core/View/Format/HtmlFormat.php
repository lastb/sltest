<?php

namespace SLTest\Core\View\Format;


use SLTest\Core\View\View;

class HtmlFormat implements FormatInterface
{
    /**
     * @inheritdoc
     */
    public function prepare(&$data, $options)
    {
        View::$headers['Content-Type'] = 'text/html;charset=utf-8';
    }
}