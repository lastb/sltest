<?php

namespace SLTest\Core\Error;


use SLTest\Core\HTTP\Exception\HttpException;
use SLTest\Core\HTTP\Helper;
use SLTest\Core\Http\Response;
use SLTest\Core\Kernel\Kernel;
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
        $message = $e->getMessage();
        if (!empty(Kernel::config()['debug'])) {
            $message .= "\n" . $e->getTraceAsString();
            $prev = $e->getPrevious();
            while ($prev) {
                $message .= "\n" . $prev->getMessage() . "\n" . $prev->getTraceAsString();
                $prev = $prev->getPrevious();
            }
        }

        $view = new View(new HtmlFormat(), $message, ['template' => 'error.html']);
        $response = $view->getResponse();
        if ($e instanceof HttpException) {
            $response->setStatus($e->getHttpStatusCode());
        } else {
            $response->setStatus(Response::HTTP_STATUS_INTERNAL_ERROR);
        }

        Helper::sendResponse($response);
    }
}