<?php

$path = dirname(__FILE__);
require_once $path . '/../bootstrap.php';

$kernel = \SLTest\Core\Kernel\Kernel::init('prod'); // @todo: get env by args
$database = new \SLTest\Core\Database\Database(\SLTest\Core\Kernel\Kernel::config());

try {
    echo "Инициализация БД.";
    $database->connect();

    $query = '
      CREATE TABLE IF NOT EXISTS {feedback} (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR (64) NOT NULL,
        `email` VARCHAR (254) NOT NULL,
        `text` TEXT NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB;';

    $database->sqlQuery($query);

} catch (Throwable $e) {
    print $e->getMessage();
}

// some indexes here.