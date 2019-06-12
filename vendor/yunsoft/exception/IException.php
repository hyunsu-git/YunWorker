<?php


namespace yun\exception;

/**
 * Interface IException
 * 所有异常都要实现该接口
 * @package yun\exception
 */
interface IException 
{
    /**
     * 抛出异常的名称,方便后期日志管理
     * @return mixed
     * @author hyunsu
     * @time 2019-06-11 11:10
     */
    public function getName();
}