<?php
    /** @var bool $has_file */
    /** @var array $test */
    /** @var string $test['action'] */
    /** @var float $test['micro_time'] */
    /** @var string $test['memory_usage'] */
    /** @var int $test['error_count'] */
    /** @var int $test['iteration_count'] */
    /** @var int $test['max_line'] */
    /** @var int $test['real_max_line'] */
?>

<h4>Генерация файла 2GB.</h4>
<a href="/tests/large-file-generate">Сгенерировать файл.</a>

<h4 class="mt-5">Тесты</h4>
<?php if (!$has_file):?>
    <p>Для запуска тестов необходимо сгенерировать файл.</p>
<?php else:?>
    <div class="mb-2">
        <b>Выбор случайных строк.</b>
        <form class="form-inline" action="/tests/large-file" method="POST" enctype="application/x-www-form-urlencoded">
            <label for="iteration-count">Iterator count</label>
            <input id="iteration-count" class="form-control mx-2" name="test-options[iteration-count]" value="<?php print ($test['iteration_count'] ?? 10000);?>" />
            <label for="max-line">Max line</label>
            <input id="max-line" class="form-control mx-2" name="test-options[max-line]" value="<?php print ($test['max_line'] ?? 26037744);?>" />
            <input name="test" value="iteration" type="hidden" />
            <button type="submit" class="btn btn-primary">Запустить тест!</button>
        </form>
        <?php if ($test['type'] == 'iteration'):?>
            <table class="table mt-3">
                <tr>
                    <td>Время выполнения:</td>
                    <td><?php print $test['micro_time'];?> сек.</td>
                </tr>
                <tr>
                    <td>Затрачено памяти:</td>
                    <td><?php print $test['memory_usage'];?> MB</td>
                </tr>
                <tr>
                    <td>Ошибки:</td>
                    <td><?php print $test['error'];?></td>
                </tr>
                <tr>
                    <td>Количество итерации:</td>
                    <td><?php print $test['iteration_count'];?></td>
                </tr>
                <tr>
                    <td>Максимальный возможный номер линии:</td>
                    <td><?php print $test['max_line'];?></td>
                </tr>
                <tr>
                    <td>Максимальный номер линии, который был указан:</td>
                    <td><?php print $test['real_max_line'];?></td>
                </tr>
            </table>
        <?php endif;?>
    </div>
    <div class="my-2">
        <b>Выбор конкретной строки</b>
        <form class="form-inline" action="/tests/large-file" method="POST" enctype="application/x-www-form-urlencoded">
            <label for="max-line"># line</label>
            <input id="max-line" class="form-control mx-2" name="test-options[position]" value="<?php print ($test['position'] ?? 0);?>" />
            <input name="test" value="position" type="hidden" />
            <button type="submit" class="btn btn-primary">Запустить тест!</button>
        </form>
        <?php if ($test['type'] == 'position'):?>
            <table class="table mt-3">
                <tr>
                    <td>Время выполнения:</td>
                    <td><?php print $test['micro_time'];?> сек.</td>
                </tr>
                <tr>
                    <td>Затрачено памяти:</td>
                    <td><?php print $test['memory_usage'];?> MB</td>
                </tr>
                <tr>
                    <td>Ошибки:</td>
                    <td><?php print $test['error'];?></td>
                </tr>
                <tr>
                    <td>Строка:</td>
                    <td><?php print $test['line'];?></td>
                </tr>
            </table>
        <?php endif;?>
    </div>
<?php endif;?>

