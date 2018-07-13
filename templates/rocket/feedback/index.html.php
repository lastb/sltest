<?php
/** @var array $items */
/** @var string $pagination */
/** @var string $callback */
/** @var bool $uri */
?>

<h4>Все обращения</h4>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Текст</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item):?>
        <tr>
            <td><?php print $item['id'];?></td>
            <td><?php print $item['name'];?></td>
            <td><?php print $item['email'];?></td>
            <td><?php print $item['text'];?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php if ($pagination) { print $pagination; }?>

<?php if ($callback === 'success'):?>
    <div class="alert alert-success">Ваше сообщение отправлено!</div>
<?php elseif (!empty($callback)):?>
    <div class="alert alert-danger"><?php print $callback;?></div>
<?php endif;?>

<h4>Форма обратной связи</h4>
<div class="row">
    <div class="col-6">
        <form action="/feed-back/add" method="POST" enctype="application/x-www-form-urlencoded">
            <div class="form-row">
                <div class="form-group col-6"><input required class="form-control" name="name" placeholder="Ваше имя" /></div>
                <div class="form-group col-6"><input required class="form-control" name="email" placeholder="Ваше email" /></div>
            </div>
            <div class="form-group">
                <textarea required class="form-control" name="text" placeholder="text сообщения"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>
</div>