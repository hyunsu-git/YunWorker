<?php


namespace yun\exception;


use yun\helpers\LangHelper;

/**
 * Class NotInstantiableException
 * 无法实例化某个类的异常,主要在工厂中抛出
 * @package yun\exception
 */
class NotInstantiableException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($class, $message = null, $code = 0, \Exception $previous = null)
    {
        if ($message === null) {
            $message = LangHelper::ts('Can not instantiate %s.', $class);
        }
        parent::__construct($message, $code, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Not Instantiable Exception';
    }
}