<?php

/**
 * 当前框架的版本号
 */

use yun\base\Application;
use yun\factory\Container;
use yun\helpers\ArrayHelper;

define('YUN_VERSION', '1.0.0');

/**
 * 程序开始运行的时间
 */
define('YUN_START_TIME', microtime(true));

/**
 * 目录分隔符简写
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * 根目录的绝对路径
 */
defined('ROOT_PATH') or define('ROOT_PATH', dirname(dirname(realpath(__DIR__))) . DS);

/**
 * 框架的核心目录
 */
defined('CORE_PATH') or define('CORE_PATH', realpath(__DIR__) . DS);

/**
 * app逻辑目录,用户主要在此目录中编写代码
 */
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . 'application' . DS);

/**
 * 第三方文件,主要是composer安装文件目录
 */
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);

/**
 * 公共文件目录
 */
defined('COMMON_PATH') or define('COMMON_PATH', ROOT_PATH . 'common' . DS);

/**
 * 是否开启debug模式
 */
defined('YUN_DEBUG') or define('YUN_DEBUG', false);

/**
 * 当前运行在正式环境还是开发环境
 */
defined('YUN_ENV') or define('YUN_ENV', 'prod');

/**
 * 用于全局判断当前是否是正式环境
 */
defined('YUN_ENV_PROD') or define('YUN_ENV_PROD', YUN_ENV === 'prod');

/**
 * 用于全局判断当前是否是开发环境
 */
defined('YUN_ENV_DEV') or define('YUN_ENV_DEV', YUN_ENV === 'dev');

//自动加载
require CORE_PATH . '/base/Loader.php';
//自动加载路径映射文件
\yun\base\Loader::$classMap = require CORE_PATH . 'classes.php';

require CORE_PATH . 'Yun.php';

//加载配置
Application::$config = ArrayHelper::merge(
    require COMMON_PATH . '/config/main.php',
    require COMMON_PATH . '/config/main-local.php'
);
//初始化工厂盒子
Application::$container = new Container();
