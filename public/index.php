<?php

require_once '../bootstrap.php';

$kernel = new \SLTest\Core\Kernel\Kernel('prod');
$response = $kernel->handleRequest(\SLTest\Core\HTTP\Helper::createRequestFromGlobals());
\SLTest\Core\HTTP\Helper::sendResponse($response);