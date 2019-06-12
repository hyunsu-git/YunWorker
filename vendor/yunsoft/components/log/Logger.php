<?php


namespace yun\components\log;


use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\ScalarFormatter;
use yun\exception\InvalidConfigException;
use yun\helpers\FileHelper;
use yun\helpers\SystemHelper;

/**
 * Class Logger
 * 日志记录器,如果要自定义日志记录器,继承这个类,重新实现[[flush()]]方法就可以
 * @package yun\components\log
 */
class Logger
{

    /**
     * @var array 记录的日志数组,这个数组通过[[log()]]和[[flush]]进行管理
     * 调用[[log()]]方法并不会直接记录日志,而是保存到该数组
     * 当数组大小 >= $flushInterval 时候,会调用[[flush()]]方法,将日志记录到指定位置
     */
    protected $messages = [];

    /**
     * @var array 资料收集数组,由[[startProfiling()]]和[[endProfiling]] 管理
     * 资料收集开始和结束会强制开启debug模式
     */
    protected $profiling = [];

    /**
     * @var int 限制$message能缓存的最大条数
     * 默认值是100,如果不想缓存,应该设置为0.表示所有日志立刻记录到指定位置
     * 此属性主要影响日志消息占用的内存量。
     * 较小的值意味着内存更少，但会由于[[flush()]]的开销而增加执行时间。
     */
    public $flushInterval = 100;

    /**
     * @var int 为每条消息记录多少调用堆栈信息。
     * 如果大于0，则最多记录该数目的调用堆栈。设置为0则表示不限制
     * 注意:此属性只有在debug模式下或者强制[[formatLogMessage()]]的第二个参数为true时才生效
     */
    public $traceLevel = 15;

    /**
     * @var string monolog组件中 Logger 名称的后缀
     */
    private $_name;

    /**
     * @var \Monolog\Logger
     */
    private $_logger = null;

    /**
     * @var string 记录日志采用的handler
     */
    public $handler = LOG_HANDLER_FILE;

    /**
     * @var string 数组的序列化方式,可以是字符串或者json序列化
     * 默认是字符串序列化,方便写入文件. 如果写入数据库之类,可以选择json序列化
     */
    public $arraySerialization = LOG_ARRAY_SERIALIZATION_STRING;

    /**
     * @var string 记录日志的文件路径
     */
    public $file = ROOT_PATH . 'runtime/log/app.log';

    /**
     * Logger constructor.
     * @param string $name
     */
    public function __construct($name = 'system')
    {
        $this->_name = $name;
    }

    /**
     * 开启一个资料收集
     * 开启资料收集后需要调用 endProfiling 方法结束收集,收集的信息会以debug级别记录到日志中
     * 收集的信息包括时间戳,内存使用信息,堆栈信息,中间调用过的sql语句
     * 收集期间,可以使用 [[addProfilingContent()]] 追加自定义信息
     * @param string $tag 收集标记,设置同一个收集标记会失败
     * @return bool
     * @see endProfiling()
     * @see addProfilingContent()
     * @author hyunsu
     * @time 2019-06-11 16:21
     */
    public function startProfiling($tag = 'debug')
    {
        if (isset($this->profiling[$tag])) {
            return false;
        }

        $this->profiling[$tag] = $this->formatLogMessage('start', true);
        $this->profiling['content'] = [];

        return true;
    }

    /**
     * 结束资料收集
     * @param null $tag 收集标记,如果不设置则结束所有收集
     * @return void
     * @see endSingleProfiling()
     * @author hyunsu
     * @time 2019-06-11 16:29
     */
    public function endProfiling($tag = null)
    {
        if ($tag !== null) {
            if ($message = $this->endSingleProfiling($tag)) {
                $this->log($message, \Monolog\Logger::DEBUG);
            }
        } else {
            foreach ($this->profiling as $pf) {
                $message = $this->endSingleProfiling($pf);
                if ($message !== false) {
                    $this->log($message, \Monolog\Logger::DEBUG);
                }
            }
        }
    }

    /**
     * 结束单个收集,计算所用时间
     * 返回一个数组,包含了开启收集时的debug信息和结束时的debug信息
     * ```
     * [
     *   ['start'] => [],  //开始收集的debug信息
     *   ['end'] => [],   //结束收集的debug信息
     *   ['content'] => [],   //收集期间执行的sql语句和自定义信息
     *   ['duration'] => 0    //所用时间
     * ]
     * ```
     * @param $tag string 收集标记
     * @return array|bool 如果不存在该标记会返回false
     * @author hyunsu
     * @time 2019-06-11 16:23
     */
    public function endSingleProfiling($tag)
    {
        if (!isset($this->profiling[$tag])) {
            return false;
        }
        $message = array();
        $message['start'] = $this->profiling[$tag];
        unset($this->profiling[$tag]);
        $message['content'] = $message['start']['content'];
        unset($message['start']['content']);
        $message['end'] = $this->formatLogMessage('end', true);
        $message['duration'] = $message['end']['timestamp'] - $message['start']['timestamp'];

        return $message;
    }


    /**
     * 在资料收集过程中追加内容
     * @param $content mixed 要追加的内容
     * @param null $tag 要追加的资料标记,默认为null,所有过程全追加
     * @return void
     * @author hyunsu
     * @time 2019-06-11 16:51
     */
    public function addProfilingContent($content, $tag = null)
    {
        if ($tag !== null) {
            if (isset($this->profiling[$tag])) {
                $this->profiling[$tag]['content'][] = $content;
            }
        } else {
            foreach ($this->profiling as &$pf) {
                $pf['content'][] = $content;
            }
        }
    }

    /**
     * 记录日志
     * @param $obj mixed 日志内容
     * @param $level int 日志级别,采用 \Monolog\Logger::* 的模式
     * @return void
     * @author hyunsu
     * @time 2019-06-11 21:18
     */
    public function log($obj, $level)
    {
        $message = $this->formatLogMessage($obj, false);

        $message['level'] = $level;

        $this->messages[] = $message;

        if ($this->flushInterval == 0 || (sizeof($this->messages) >= $this->flushInterval)) {
            $this->flush();
        }
    }


    /**
     * 对日志信息进行格式化
     * 追加时间戳信息,如果是debug模式,追加内存使用信息和堆栈信息(文件和行数)
     * @param $obj mixed
     * @param $debug boolean 强制开启debug
     * @return array
     * @author hyunsu
     * @time 2019-06-11 16:03
     */
    public function formatLogMessage($obj, $debug)
    {
        $message = ['message' => $obj, 'timestamp' => microtime(true)];

        if ($debug || YUN_DEBUG === true || YUN_DEBUG == 1) {

            if ($obj instanceof \Exception) {
                $message['trace'] = [];
            } else {
                $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 100);
                //去掉第一层堆栈,也就是方法自身
//                if (isset($trace[0]) && isset($trace[0]['function']) && $trace[0]['function'] == 'formatLogMessage') {
//                    unset($trace[0]);
//                }

                $message['trace'] = [];

                foreach ($trace as $item) {
                    if (isset($item['file'])) {
                        $message['trace'][] = '/' . str_replace(ROOT_PATH, '', "{$item['file']} on line {$item['line']}");
                    } else if (isset($item['class']) && isset($item['function'])) {
                        $message['trace'][] = "[memory] call {$item['class']}\\{$item['function']}";
                    }
                }
            }

            $message['usage'] = [];
            $message['usage'][] = SystemHelper::memory_get_usage();
            $message['usage'][] = SystemHelper::memory_get_peak_usage();
        }

        return $message;
    }


    /**
     * 将缓存的日志记录到指定位置并清空缓存
     * @return void
     * @author hyunsu
     * @time 2019-06-12 09:43
     */
    public function flush()
    {
        if ($this->handler == LOG_HANDLER_FILE) {
            $this->flushFile();
        }


        $this->messages = [];
    }

    /**
     * 将缓存中的日志固化到文件中
     * @return void
     * @author hyunsu
     * @time 2019-06-12 09:42
     */
    private function flushFile()
    {
        foreach ($this->messages as $msg) {
            $level = $msg['level'];
            unset($msg['level']);

            $t = date("Y-m-d H:i:s", $msg['timestamp']);
            unset($msg['timestamp']);

            if ($this->arraySerialization == LOG_ARRAY_SERIALIZATION_JSON && is_object($msg['message'])) {
                $msg['message'] = sprintf($msg['message'], true);
                $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
            } else {
                $msg = print_r($msg, true);
            }

            $this->getLogger()->log($level, $msg, [$t]);
        }

        FileHelper::createLogFile($this->file);

        file_put_contents($this->file, FlushHandler::getLogsToString());
    }


    /**
     * 获取缓存的所有日志
     * @return array
     * @author hyunsu
     * @time 2019-06-11 21:11
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * 获取现在开启中的资料收集
     * @return array
     * @author hyunsu
     * @time 2019-06-11 21:20
     */
    public function getProfiling()
    {
        return $this->profiling;
    }

    /**
     * 获取 Monolog 的实例,第一次获取会先实例化
     * @return \Monolog\Logger
     * @author hyunsu
     * @time 2019-06-12 08:55
     */
    private function getLogger()
    {
        if ($this->_logger == null) {
            $this->_logger = new \Monolog\Logger('yun_logger_' . $this->_name);

            if ($this->handler == LOG_HANDLER_FILE) {
                $stream = new FlushHandler();
                $stream->setFormatter(FlushHandler::format());
                $this->_logger->pushHandler($stream);
            }
        }

        return $this->_logger;
    }
}