# sltest
SL test example

Пример решения тестового задания.

Для начала необходимо создать файл config/config.prod.php, с примерно таким содержанием:
```
return array(
    '@include' => 'config.php',
    '@data' => array(
        'database' => array(
            'driver' => 'mysql',
            'user' => '[ИМЯ ПОЛЬЗОВАТЕЛЯ В БД]',
            'db' => '[ИМЯ БД]',
            'host' => 'localhost',
            'port' => 3306,
            'password' => '[ПАРОЛЬ ОТ БД]',
            'prefix' => '[ПРЕФИКС ТАБЛИЦ]'
        ),
    ),
);
```

Затем запустить скрипт инициализации БД scripts/database_init.php.