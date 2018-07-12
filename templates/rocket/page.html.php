<?php
/** @var string $title */
/** @var array $heads */
/** @var string $content */
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php print $title;?></title>
    <?php foreach ($heads as $head):?>
        <?php print $head;?>
    <?php endforeach;?>
</head>
<body>
<div><?php print $content;?></div>
</body>
</html>
