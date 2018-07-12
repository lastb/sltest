<?php
/** @var \Exception $error; */
/** @var bool $debug */
?>
<!DOCTYPE html>
<html>
<body>
    <div><?php print $error->getMessage();?></div>
    <?php if ($debug):?>
        <div><?php print $error->getTraceAsString();?></div>
        <?php $prev = $error->getPrevious();?>
        <?php while ($prev):?>
            <div><?php $prev->getPrevious();?></div>
            <div><?php $prev->getTraceAsString();?></div>
        <?php endwhile;?>
    <?php endif;?>
</body>
</html>
