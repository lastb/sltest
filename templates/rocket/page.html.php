<?php
/** @var string $title */
/** @var array $heads */
/** @var array $scripts */
/** @var string $uri */
/** @var string $content */
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php print $title;?></title>
    <?php foreach ($heads as $head) { print $head . "\n"; }?>
</head>
<body>
    <header class="pt-5 bg-light"></header>
    <div class="container mt-3">
        <?php if ($uri != '/') :?><div class="mb-3"><a href="/">Главная</a></div><?php endif;?>
        <div><?php print $content;?></div>
    </div>

    <?php foreach ($scripts as $script) { print $script . "\n"; }?>
</body>
</html>
