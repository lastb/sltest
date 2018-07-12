<?php

/**
 * Формирует вывод трейса ошибки.
 *
 * @param Throwable $error
 * @return string
 */
function formatTrace(Throwable $error)
{
    $output = '';
    foreach ($error->getTrace() as $i => $trace) {
        $output .= "<li>#{$i} {$trace['file']}({$trace['line']}) {$trace['class']}{$trace['type']}{$trace['function']}";
    }
    $output = '<ul>' . $output . '</ul>';

    return $output;
}

/** @var \Exception $error текщуая ошибка. */
/** @var int $status_code http-статус код ошибки. */
/** @var bool $debug параметр конфигурации. */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ошибка :( <?php print $status_code;?></title>
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css" >
</head>
<body>
    <div class="container my-3">
        <div class="alert alert-danger">
            <?php print $error->getMessage();?>
            <?php if ($debug):?>
                <?php print formatTrace($error);?>
                <?php
                $prev = $error->getPrevious();
                while ($prev) {
                    print $prev->getMessage();
                    print formatTrace($prev);
                    $prev = $prev->getPrevious();
                }?>
            <?php endif;?>
        </div>
    </div>

    <script src="/assets/js/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script src="/assets/js/bootstrap.bundle.min.js" type="text/javascript"></script>
</body>
</html>
