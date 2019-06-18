<?php


namespace yun\dispatchers;


use yun\base\Application;

class Dispatcher implements IDispatcher
{

    /**
     * @var string 对`行为`错误的数据采取的处理方式
     * 可以设置为 ACTION_ERROR_* 定义的行为
     */
    public $actionErrorMode = ACTION_ERROR_EXCEPTION;

    /**
     * @var string 对于无效信息的处理方式
     * 设置为 INVALID_MESSAGE_* 定义的方式,可以抛出错误或者不处理
     * 判定无效的条件: 不是字符串格式|json解析失败|没有定义`行为`字段
     */
    public $invalidMessageMode = INVALID_MESSAGE_EXCEPTION;

    /**
     * {@inheritdoc}
     */
    public function receive($message)
    {
        return;
    }

    /**
     * 默认行为处理
     * @param null $param
     * @return void
     * @author hyunsu
     * @time 2019-06-17 17:43
     */
    public function defaultPlay($param = null)
    {
        $class = '\yun\controllers\SiteController';
        $fun = 'actionIndex';
        $obj = Application::createObject($class);
        if ($param != null) {
            call_user_func(array($obj, $fun), $param);
        } else {
            call_user_func(array($obj, $fun));
        }
    }

    /**
     * 将信息发送到用户自定义的处理方法
     * @param $class string 类名
     * @param $fun string  方法名
     * @param null|mixed $param 传入的参数
     * @return void
     * @throws \yun\exception\NotInstantiableException
     * @author hyunsu
     * @time 2019-06-17 17:10
     */
    public function play($class, $fun, $param = null)
    {
        $class = ucfirst($class);

        $class = "\\app\\controllers\\{$class}Controller";

        $obj = Application::createObject($class);

        $fun = ucfirst($fun);

        $fun = 'action' . $fun;

        if ($param != null) {
            call_user_func(array($obj, $fun), $param);
        } else {
            call_user_func(array($obj, $fun));
        }
    }
}