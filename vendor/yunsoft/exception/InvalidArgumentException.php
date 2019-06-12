<?php


namespace yun\exception;

/**
 * Class InvalidArgumentException
 * yun框架的基本异常类
 * Exception, InvalidArgumentException,
 * 框架中的自定义 Exception 都应该继承自这几个类中的一个,并且拥有自己的getName方法
 * @package yun\base
 */
class InvalidArgumentException extends \InvalidArgumentException implements IException
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
       return 'Invalid Argument Exception';
    }

}