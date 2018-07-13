<?php
/** @var string $uri_path */
/** @var int $nav_id */
/** @var int|false $prev_id */
/** @var int|false $next_id */
/** @var int $limit */
/** @var bool $has_remaining */
?>

<nav aria-label="page navigation">
    <ul class="pagination">
    <?php if ($prev_id !== false):?>
        <li class="page-item"><a href="<?php print "{$uri_path}?nav_id={$prev_id}&limit={$limit}&forward=0";?>" class="page-link">&larr;</a></li>
    <?php endif;?>
    <?php if ($next_id !== false):?>
        <li class="page-item"><a href="<?php print "{$uri_path}?nav_id={$next_id}&limit={$limit}";?>" class="page-link">&rarr;</a></li>
    <?php endif;?>
    </ul>
</nav>

