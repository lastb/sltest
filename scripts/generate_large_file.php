<?php

const lineLimit = 80;
const chunkSize = 32768;
const chunkAmount = 65536;

$path = dirname(__FILE__);
require_once $path . '/../bootstrap.php';

$kernel = \SLTest\Core\Kernel\Kernel::init('prod'); // @todo: get env by args

\SLTest\Core\Generator\Helper::generateLargeFile(
    ASSETS_DIR . '/generator/words.txt',
    \SLTest\App\Controllers\Tests::LARGE_FILE_PATH,
    lineLimit,
    chunkSize,
    chunkAmount
);
