<?php

//只能命令行启动
if (PHP_SAPI != 'cli') {
    exit("Error: ThinkWorker can only run under CLI mode.");
}

defined('YUN_DEBUG') or define('YUN_DEBUG', true);
defined('YUN_ENV') or define('YUN_ENV', 'dev');
//defined('YUN_ENV') or define('YUN_ENV', 'prod');

require "vendor/yunsoft/init.php";

\yun\base\Loader::setAlias("@app", APP_PATH);

\yun\base\Application::run();