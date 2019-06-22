<?php

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$process = require __DIR__ . '/process.php';

return [
    'appName' => 'common',
    'eventFile' => '\app\Event',
    'language' => 'zh-CN',
    'gateway' => [
        //内部通讯设置,客户端不要连接这里设置的端口和地址
        'register' => [
            'listen' => '0.0.0.0:5109',//必须
            'ip' => '127.0.0.1',  //必须
        ],
        //gateway相关设置
        'gateway' => [
            //用户客户端连接的地址,
            'listen' => 'websocket://0.0.0.0:7070',   //必须
            // gateway名称，status方便查看
            'name' => 'DDW-gateway',
            // gateway进程数
            'processCount' => 4,
            // 本机ip，分布式部署时使用内网ip
            'ip' => '127.0.0.1',
            // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
            // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
            'startPort' => 7700,
        ],
        //business 相关设置
        'business' => [
            // bussiness名称
            'name' => 'DDW-business',
            //bussiness 进程数量
            'processCount' => 4,
            //bussiness 进程详细配置
            'process' => $process
        ],
        //心跳相关设置
        'ping' => [
            // 心跳间隔,秒
            'interval' => 30,
            // 心跳数据
            'data' => '{"act":"heartbeat"}'
        ],
    ],


    'components' => [
        'encrypt' => [
            'class' => '\yun\components\ResponseEncrypt',
            'RsaPrivateKey' => ROOT_PATH . 'runtime/rsa_private_key.pem',
        ],
        'log' => [
            'class' => '\yun\components\log\Logger',
            'handler' => LOG_HANDLER_FILE,
        ],
//        'dispatcher'=>false,    //禁用分配器
        'dispatcher' => [
            'class' => '\yun\dispatchers\JsonDispatcher',
//            'actField'=>'act',
//            'dataField'=>'data',
//            'delimiter'=>'.'
//            'actionErrorMode'=>ACTION_ERROR_NULL
        ],
        'redis' => [
            'class' => '\yun\components\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
//            'password'=>'',
        ],

        'mysql' => [
            'class' => '\yun\components\mysql\Connection',
            'host' => 'localhost',
            'port' => 3306,
            'user' => '',
            'password' => '',
            'dbname' => '',
            'charset' => 'utf8'
        ]
    ],


    'params' => $params,
];