<?php


namespace yun\exception;


use yun\helpers\LangHelper;


/**
 * Class InvalidConfigException
 * 配置项无效抛出的异常
 * @package yun\exception
 */
class InvalidConfigException extends InvalidArgumentException
{

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $message = null, $code = 0, \Exception $previous = null)
    {
        $m = '';
        
        if ($name) {
            $m = LangHelper::ts('Configuration `%s` is invalid.', $name);
        }
        if ($message !== null) {
            $message = $m . "\r\n" . $message;
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Invalid Config Exception';
    }
}