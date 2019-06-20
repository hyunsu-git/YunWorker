<?php

use yun\components\log\Logger;

class YunBase
{
    /**
     * @var \yun\base\Worker[]
     */
    public static $workers = [];

    /**
     * @var Logger 这个记录器与进程无关,是系统级别的全局记录器
     * 用户记录日志应该使用 \yun\base\Worker 中的记录器
     */
    private static $logger;

    /**
     * 便捷方法,获取当前环境
     * @return string
     * @author hyunsu
     * @time 2019-06-11 10:42
     */
    public static function getDev()
    {
        return YUN_ENV;
    }

    /**
     * 便捷方法,是否是debug模式
     * 注意,debug模式不代表当前环境,即使生成环境,也可以处于debug模式
     * @return boolean
     * @author hyunsu
     * @time 2019-06-11 10:43
     */
    public static function isDebug()
    {
        return YUN_DEBUG;
    }


    /**
     * 返回程序运行时长
     * @param bool $micro 是否返回毫秒,默认返回秒
     * @return int 从程序开始运行到现在的时间
     * @author hyunsu
     * @time 2019-06-11 10:36
     */
    public static function getRuntime($micro = false)
    {
        //todo
        return 0;
    }


    /**
     * 获取日志记录器的实例
     * @return mixed|Logger
     * @author hyunsu
     * @time 2019-06-11 15:41
     */
    public static function getLogger()
    {
        if (self::$logger != null) {
            return self::$logger;
        }

        self::$logger = \yun\base\Application::createObject(\yun\base\Application::getComponents('log'), ['system']);
        self::$logger->flushInterval = 0;

        return self::$logger;
    }


    /**
     * 增加一个worker实例
     * 该方法应该在 系统的 onWorkerStart 中调用
     * 用户的 onWorkerStart 不需要调用该方法
     * @param \GatewayWorker\BusinessWorker $businessworker
     * @return void
     * @author hyunsu
     * @time 2019-06-11 17:39
     */
    public static function addWorker(\GatewayWorker\BusinessWorker $businessworker)
    {
        self::$workers[$businessworker->id] = new \yun\base\Worker($businessworker);
    }


    /**
     * 获取当前进程的 worker 实例
     * @return \yun\base\Worker|null
     * @author hyunsu
     * @time 2019-06-11 23:05
     */
    public static function getWorker()
    {
        global $_business_worker_id;

        return isset(self::$workers[$_business_worker_id]) ? self::$workers[$_business_worker_id] : null;
    }

    /**
     * 获取 param.php 文件中写的变量
     * @param $key string 配置的键,可以是 aa.bb.cc的形式
     * @param null $default 如果没有设置这个键,默认返回的值
     * @return array|null
     * @author hyunsu
     * @time 2019-06-20 17:32
     */
    public static function getParam($key,$default = null)
    {
        $config = \yun\base\Application::$params;

        $ary_key = explode('.', $key);

        foreach ($ary_key as $k) {
            if (isset($config[$k])) {
                $config = $config[$k];
            } else {
                $config = null;
            }
        }

        return !$config ? $default : $config;
    }

    /**
     * 设置 params 中存储的变量
     * 关于合并和覆盖的方式
     * @see \yun\helpers\ArrayHelper::merge();
     * @see getParams()
     * @param $key
     * @param $value
     * @return void
     * @author hyunsu
     * @time 2019-06-20 17:50
     */
    public static function setParam($key, $value)
    {
        $ary_key = explode('.', $key);

        $customer_param = [];

        $temp = &$customer_param;

        foreach ($ary_key as $k) {
            $temp[$k] = [];
            $temp = &$temp[$k];
        }

        $temp = $value;

        $params = \yun\base\Application::$params;

        \yun\base\Application::$params = \yun\helpers\ArrayHelper::merge($params, $customer_param);
    }

}
