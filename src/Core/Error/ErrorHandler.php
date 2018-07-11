<?php

namespace SLTest\Core\Error;


use SLTest\Core\HTTP\Exception\HttpException;
use SLTest\Core\HTTP\Helper;
use SLTest\Core\Http\Response;
use SLTest\Core\View\Format\HtmlFormat;
use SLTest\Core\View\View;

class ErrorHandler
{
    /**
     * Обработка ошибок для правльной работы исключений.
     *
     * @param \Throwable $e
     */
    public function exceptionHandler(\Throwable $e)
    {
        $view = new View(new HtmlFormat(), $e->getMessage(), ['template' => 'error.html']);
        $response = $view->getResponse();
        if ($e instanceof HttpException) {
            $response->setStatus($e->getHttpStatusCode());
        } else {
            $response->setStatus(Response::HTTP_STATUS_INTERNAL_ERROR);
        }

        Helper::sendResponse($response);
    }
}