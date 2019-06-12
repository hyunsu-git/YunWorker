<?php


namespace yun\components\log;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Class FlushHandler
 * 把缓存的消息格式化
 * @package yun\components\log
 */
class FlushHandler extends AbstractProcessingHandler
{
    /**
     * @var array 格式化后的日志数组
     */
    public static $logs = [];

    /**
     * 获取格式化后的数组
     * @return array
     * @author hyunsu
     * @time 2019-06-12 08:52
     */
    public static function getLogs()
    {
        return self::$logs;
    }

    /**
     * 将格式化后的日志数组转成字符串返回
     * @return string
     * @author hyunsu
     * @time 2019-06-12 08:53
     */
    public static function getLogsToString()
    {
        $str = '';
        foreach (self::$logs as $log) {
            $str .= $log;
        }

        return $str;
    }

    /**
     * 对数据进行格式化
     * 注意:这里格式化中并没有 message 变量,所以在记录的时候单独记录了一次message
     * @return LineFormatter
     * @author hyunsu
     * @time 2019-06-10 17:17
     */
    public static function format()
    {
        $dateFormat = 'Y-m-d H:i:s';

        $output = "%context%  %channel%.%level_name%:\r\n%message%\r\n";

        $formatter = new LineFormatter($output, $dateFormat, true);

        return $formatter;
    }


    protected function write(array $record)
    {
        self::$logs[] = $record['formatted'] . "\r\n";
    }
}