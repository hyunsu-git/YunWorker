<?php


namespace yun\exception;


class RedisException extends Exception
{
    public function getName()
    {
        return 'Redis Exception';
    }
}