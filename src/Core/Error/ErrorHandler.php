<?php

namespace SLTest\Core\Error;


use SLTest\Core\HTTP\Exception\HttpException;
use SLTest\Core\HTTP\Helper;
use SLTest\Core\Http\Response;
use SLTest\Core\Kernel\Kernel;
use SLTest\Core\Theme\Theme;
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
        $status_code = $e instanceof HttpException ? $e->getHttpStatusCode() : Response::HTTP_STATUS_INTERNAL_ERROR;
        $view = new View(new HtmlFormat(), Theme::render('error', [
            'error' => $e,
            'status_code' => $status_code,
            'debug' => Kernel::config()['debug']
        ]));
        $response = $view->getResponse();
        $response->setStatus($status_code);

        Helper::sendResponse($response);
    }
}