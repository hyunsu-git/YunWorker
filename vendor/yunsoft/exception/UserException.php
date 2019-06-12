<?php


namespace yun\exception;


/**
 * Class UserException
 * 用户自定义的异常应该继承此方法
 * @package yun\exception
 */
class UserException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'UserException';
    }
}