<?php


namespace yun\base;


use GatewayWorker\BusinessWorker;
use yun\components\log\Logger;

/**
 * Class Worker
 * 每个BusinessWorker进程启动的时候都会实例化一个类存放到Yun的workers中
 * 类中存放的是每个进程独立的组件或者对象,例如 日志组件,数据库连接等
 * @package yun\base
 */
class Worker
{
    /**
     * @var int worker进程的id
     */
    public $id;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var BusinessWorker
     */
    private $business_worker;


    /**
     * Worker constructor.
     * @param BusinessWorker $businessWorker
     */
    public function __construct(BusinessWorker $business_worker)
    {
        $this->id = $business_worker->id;

        $this->init();
    }

    /**
     * 初始化函数,设置全局 id ,实例化一些组件
     * @return void
     * @author hyunsu
     * @time 2019-06-11 20:57
     */
    private function init()
    {
        global $_business_worker_id;

        $_business_worker_id = $this->id;

        $this->logger = new Logger();
    }

    /**
     * 记录 info 级别的日志
     * @param $msg mixed 要记录的内容
     * @return void
     * @author hyunsu
     * @time 2019-06-11 20:58
     */
    public function info($msg)
    {
        $this->logger->log($msg, \Monolog\Logger::INFO);
    }

    /**
     * 记录 warning 级别的日志
     * @param $msg mixed 要记录的内容
     * @return void
     * @author hyunsu
     * @time 2019-06-11 20:58
     */
    public function warning($msg)
    {
        $this->logger->log($msg, \Monolog\Logger::WARNING);
    }

    /**
     * 记录 debug 级别的日志
     * @param $msg mixed 要记录的内容
     * @return void
     * @author hyunsu
     * @time 2019-06-11 20:58
     */
    public function debug($msg)
    {
        $this->logger->log($msg, \Monolog\Logger::DEBUG);
    }

    /**
     * 记录 error 级别的日志
     * @param $msg mixed 要记录的内容
     * @return void
     * @author hyunsu
     * @time 2019-06-11 20:58
     */
    public function error($msg)
    {
        $this->logger->log($msg, \Monolog\Logger::ERROR);
    }

}