<?php


namespace yun\base;


use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Worker;
use yun\components\log\Logger;
use yun\exception\InvalidConfigException;
use yun\factory\Container;
use yun\helpers\ArrayHelper;
use yun\helpers\LangHelper;

class Application
{
    /**
     * @var array 用于国际化的语言集合
     * 这里会存储所有语种的语言键值对,语言会在第一次使用的时候载入
     */
    public static $lang = [];

    /**
     * @var array 全局设置的数组
     * 存储了所有配置文件中的配置,关于配置的获取和设置
     * @see [[\yun\base\Config::get()]]
     * @see [[\yun\base\Config::set()]]
     */
    public static $config;

    /**
     * @var Container
     */
    public static $container = null;

    /**
     * @var Logger
     */
    private static $logger = null;

    /**
     * 框架启动方法
     * 这个方法会启动 gateway的进程,初始化用户的监听事件
     * @return void
     * @throws \yun\exception\NotInstantiableException
     * @author hyunsu
     * @time 2019-06-11 11:45
     */
    public static function run()
    {
        self::runRegisterServer();
        self::runBusinessWorker();
        self::runGateWay();

        self::initUserEvent();

        Worker::runAll();
    }



    /**
     * 获取一种语言的数组,第一次加载该语言后会缓存到$lang中
     * 不论是否有这种语言的配置文件,都会缓存,无配置会缓存空数组
     * @param $lang_str 语言名称,对应了文件名称
     * 例如 zh-CN 语言对应 languages目录下的 zh-CN.php 文件,文件直接返回一维数组
     * @return array 语言配置数组
     * @author hyunsu
     * @time 2019-06-05 13:19
     */
    public static function getLanguage($lang_str)
    {
        if (!isset(self::$lang[$lang_str])) {
            self::$lang[$lang_str] = LangHelper::loadByName($lang_str);
        }
        return self::$lang[$lang_str];
    }


    /**
     * 初始化用户的事件响应文件
     * @return void
     * @throws Exception
     * @throws \yun\exception\NotInstantiableException
     * @author hyunsu
     * @time 2019-06-06 13:43
     */
    public static function initUserEvent()
    {
        $event_file = Config::get('eventFile');

        if (!$event_file) {
            throw new InvalidConfigException('eventFile', LangHelper::ts('Configure cannot be empty.'));
        }

        $event = self::createObject($event_file);

        if (!$event instanceof \yun\console\Event) {
            throw new InvalidConfigException('eventFile',LangHelper::ts("User's event file must inherit from \yun\console\Event"));
        }

        Event::$eventHandle = $event;
    }

    /**
     * 启动Gateway进程
     * @return void
     * @author hyunsu
     * @time 2019-06-05 14:54
     */
    private static function runGateWay()
    {
        // gateway 进程，这里使用Text协议，可以用telnet测试
        $address = Config::get('gateway.gateway.listen');
        if (!$address) {
            throw new InvalidConfigException('gateway.gateway.listen',LangHelper::ts("Configure cannot be empty."));
        }
        $gateway = new Gateway($address);
        // gateway名称，status方便查看
        $name = Config::get('gateway.gateway.name');
        if (!$name) {
            $name = 'YunGateway';
        }
        $gateway->name = $name;
        // gateway进程数
        $process = intval(Config::get('gateway.gateway.process'));
        if (!$process) {
            $process = 4;
        }
        $gateway->count = $process;

        // 本机ip，分布式部署时使用内网ip
        $ip = Config::get('gateway.gateway.ip');
        if (!$ip) $ip = '127.0.0.1';
        $gateway->lanIp = $ip;

        // 内部通讯起始端口
        $start_port = Config::get('gateway.gateway.startPort');
        if (!$start_port) {
            $start_port = 7100;
        }
        $gateway->startPort = $start_port;

        // 服务注册地址
        $gateway->registerAddress = self::getRegsiterAddress();

        $ping_int = intval(Config::get("gateway.ping.interval"));
        $ping_str = Config::get('gateway.ping.data');
        if ($ping_int && $ping_str) {
            if (!is_string($ping_str)) {
                $ping_str = json_encode($ping_str);
            }
            // 心跳间隔
            $gateway->pingInterval = $ping_int;
            // 心跳数据
            $gateway->pingData = $ping_str;
        }

    }

    /**
     * 启动bussinessWorker进程
     * @return void
     * @author hyunsu
     * @time 2019-06-05 14:45
     */
    private static function runBusinessWorker()
    {
        $worker = new BusinessWorker();
        // worker名称
        $name = Config::get('gateway.business.name');
        if (!$name) {
            $name = "YunBusinessWorker";
        }
        $worker->name = $name;

        // bussinessWorker进程数量
        $process = intval(Config::get('gateway.business.process'));
        if (!$process) {
            $process = 4;
        }
        $worker->count = $process;

        // 服务注册地址
        $worker->registerAddress = self::getRegsiterAddress();

        $worker->eventHandler = '\yun\base\Event';
    }

    /**
     * 根据配置解析注册服务器地址
     * @return string
     * @author hyunsu
     * @time 2019-06-11 11:44
     */
    private static function getRegsiterAddress()
    {
        $listen = $address = Config::get('gateway.register.listen');
        $ary_listen = explode(":", $listen);
        $port = intval($ary_listen[1]);

        $register_address = Config::get('gateway.register.ip');
        if (!$register_address) $register_address = '127.0.0.1';

        return $register_address . ':' . $port;
    }

    /**
     * 启动进程间内部通讯的注册服务器
     * @return void
     * @author hyunsu
     * @time 2019-06-05 14:07
     */
    private static function runRegisterServer()
    {
        $listen = $address = Config::get('gateway.register.listen');
        if (!$listen) {
            throw new InvalidConfigException('gateway.register.listen',LangHelper::ts('Configure cannot be empty.'));
        }
        $ary_listen = explode(":", $listen);
        if (sizeof($ary_listen) != 2 || !$ary_listen[0] || !$ary_listen[1]) {
            throw new InvalidConfigException('gateway.register.listen',LangHelper::ts('Gateway register service listener address is invalid.'));
        }
        new Register("text://{$ary_listen[0]}:{$ary_listen[1]}");
    }

    /**
     * 创建一个对象
     * @param $type 接收字符串或者包含 'class' 键的数组
     * @param array $definition
     * @return mixed
     * @throws \yun\exception\NotInstantiableException
     * @see \yun\factory\Container
     * @author hyunsu
     * @time 2019-06-06 13:03
     */
    public static function createObject($type, array $definition = [])
    {
        if (is_string($type)) {
            return self::$container->get($type, $definition);
        } else if (is_array($type)) {
            if (!isset($type['class'])) {
                throw new InvalidConfigException(LangHelper::ts('Object configuration must be an array containing a `class` element.'));
            }
            $class = $type['class'];
            unset($type['class']);
            return self::$container->get($class, $definition, $type);
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * 核心组件的默认值
     * @return array
     * @author hyunsu
     * @time 2019-06-11 21:30
     */
    public static function coreComponents()
    {
        return [
            'log'=>'\yun\components\log\Logger',
        ];
    }

    /**
     * 获取指定组件的相关配置
     * @param $name string 组件名称
     * @return mixed
     * @author hyunsu
     * @time 2019-06-11 21:32
     */
    public static function getComponents($name)
    {
        $core = self::coreComponents();

        $config = Config::get('components');

        if ($config) {
            $core = ArrayHelper::merge($core, $config);
        }

        return $core[$name];
    }


}