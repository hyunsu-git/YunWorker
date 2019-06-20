<?php
return [
    'components'=>[
        'redis'=>[
            'class'=>'\yun\components\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
            'password'=>'123456,./',
        ],

        'mysql'=>[
            'class'=>'\yun\components\mysql\Connection',
            'host'=>'localhost',
            'port'=>3306,
            'user'=>'root',
            'password'=>'123456,./',
            'dbname'=>'ddw',
            'charset'=>'utf8'
        ]
    ]
];
