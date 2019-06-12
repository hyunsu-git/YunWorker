<?php


namespace yun\exception;


/**
 * Class Exception
 * yun框架的基本异常类包括 Exception, InvalidArgumentException,
 * 框架中的自定义 Exception 都应该继承自这几个类中的一个,并且拥有自己的getName方法
 * @package yun\exception
 */
class Exception extends \Exception implements IException
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Exception';
    }
}