<?php


namespace yun\console;


class Controller
{
    /**
     * 执行action前触发
     * 返回false则不执行接下来的action
     * @param string $action 将要执行的action方法名称
     * @return bool false中断执行 true不做处理
     * @author hyunsu
     * @time 2019-06-17 16:30
     */
    public function beforeAction($action = '')
    {
        return true;
    }

    
}