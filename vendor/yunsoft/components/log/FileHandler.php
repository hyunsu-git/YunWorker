<?php


namespace yun\components\log;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use yun\helpers\FileHelper;

class FileHandler extends AbstractProcessingHandler
{
    /**
     * @var string 文件名称,不需要目录和后缀名
     */
    public $filename;

    public function __construct($filename='app', $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->filename = $filename;
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

        $output = "[%datetime%] %channel%.%level_name%:\r\n%context% %extra%\r\n";

        $formatter = new LineFormatter($output, $dateFormat);

        return $formatter;
    }

    /**
     * 将数据记录到文件
     * @param array $record
     * @return void
     * @author hyunsu
     * @time 2019-06-10 17:17
     */
    protected function write(array $record)
    {
        $path = FileHelper::createLogFile(ROOT_PATH . "runtime", $this->filename);

        file_put_contents($path, $record['formatted'], FILE_APPEND);
        file_put_contents($path, $record['message'], FILE_APPEND);
        file_put_contents($path, "\r\n\r\n\r\n", FILE_APPEND);
    }
}