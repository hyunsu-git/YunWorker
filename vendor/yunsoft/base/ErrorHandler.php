<?php


namespace yun\base;


use yun\exception\ErrorException;
use yun\exception\Exception;
use yun\exception\InvalidArgumentException;

/**
 * Class ErrorHandler
 * @package yun\base
 */
class ErrorHandler
{
    /**
     * ErrorHandler constructor.
     */
    public function __construct()
    {
        if(YUN_ENV === 'dev'){
            ini_set('display_errors', 'On');
            error_reporting(E_ALL | E_STRICT);
        }else{
            ini_set('display_errors', 'Off');
            error_reporting(0);
        }
    }

    /**
     * 注册错误处理的自定义函数
     * @return void
     * @author hyunsu
     * @time 2019-06-12 14:50
     */
    public function register()
    {
        set_error_handler([$this, 'handleError']);

        set_exception_handler([$this, 'handleException']);

        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * 将php的错误转换成异常
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @return void
     *
     * @throws \ErrorException
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * 将异常转换成包括详细信息的字符串
     * @param $exception
     * @return string
     * @see \backend\models
     * @see \frontend\models
     * @author hyunsu
     * @time 2019-06-10 14:59
     */
    public static function convertExceptionToVerboseString($exception)
    {
        if ($exception instanceof Exception) {
            $message = "Exception ({$exception->getName()})";
        } elseif ($exception instanceof ErrorException) {
            $message = (string)$exception->getName();
        } else {
            $message = 'Exception';
        }
        $message .= " '" . get_class($exception) . "' with message '{$exception->getMessage()}' \r\nin "
            . $exception->getFile() . ':' . $exception->getLine() . "\r\n"
            . "Stack trace:\r\n" . $exception->getTraceAsString();

        return $message;
    }

    /**
     * 对异常进行处理
     * @param $exception Exception
     * @return void
     * @author hyunsu
     * @time 2019-06-10 16:13
     */
    public function handleException($exception)
    {
        $ret = array();

        if ($exception instanceof Exception || $exception instanceof InvalidArgumentException) {
            $message = $exception->getName();
        }else if ($exception instanceof ErrorException) {
            $message = $exception->getName();
        }else{
            $message = 'Exception';
        }

        $message .= "\t" . get_class($exception). ': ';
        $message .= $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\r\n";

        $ret = [
            'message' => $message,
            'timestamp' => microtime(true),
            'trace' => $exception->getTraceAsString(),
        ];

        \Yun::getLogger()->log($ret, 400, false);
    }


    /**
     * @return void
     * @author hyunsu
     * @time 2019-06-10 15:00
     */
    public function handleShutdown()
    {
        $error = error_get_last();

        require_once CORE_PATH . '/exception/ErrorException.php';

        if (ErrorException::isFatalError($error)) {

            $exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);

            \Yun::getLogger()->log($exception, 400);
        }

        \Yun::getLogger()->flush();

        if (is_array(\Yun::$workers)) {
            foreach (\Yun::$workers as $worker) {
                $worker->logger->flush();
            }
        }

        exit(-1);
    }
}