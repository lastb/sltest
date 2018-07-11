<?php

require_once '../bootstrap.php';

$kernel = \SLTest\Core\Kernel\Kernel::init('prod');
$response = $kernel->handleRequest(\SLTest\Core\HTTP\Helper::createRequestFromGlobals());
\SLTest\Core\HTTP\Helper::sendResponse($response);