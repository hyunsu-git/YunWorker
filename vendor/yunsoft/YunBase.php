<?php

use yun\components\log\Logger;

class YunBase
{
    /**
     * @var \yun\base\Worker[]
     */
    public static $workers;

    /**
     * @var Logger
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

        return self::$logger = \yun\base\Application::createObject(\yun\base\Application::getComponents('log'),['system']);
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
}
