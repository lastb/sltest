<?php

return array(
    '@data' => array(
        'database' => array(
            'driver' => 'mysql',
            'db' => 'sltest',
            'host' => 'localhost',
            'port' => 3306,
            'password' => 'GodSexLove',
            'prefix' => 'slt'
        ),
        'pdo' => array(
            'ATTR_ERRMODE' => 'ERRMODE_EXCEPTION',
            'ATTR_DEFAULT_FETCH_MODE' => 'FETCH_ASSOC',
            'ATTR_ORACLE_NULLS' => 'NULL_TO_STRING'
        ),
        'debug' => true,
    )
);