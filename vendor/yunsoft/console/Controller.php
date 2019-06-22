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


    /**
     * 验证一个model并且调用其 execute() 方法
     * 如果验证成功并且也 execute() 返回true 则回调 success 方法,否则会回调 fail 方法
     * @param Model $model
     * @param callable|null $success 成功的回调方法
     * @param callable|null $fail 失败的回调方法
     * @return bool
     * @author hyunsu
     * @time 2019-06-21 14:16
     */
    public function executeModel(Model $model, callable $success = null, callable $fail = null)
    {
        if ($model->validate()) {
            $result = $model->execute();
            if ($result === 0 || $result === [] || $result ) {
                if ($success != null) {
                    $success($result);
                }
                return true;
            }
        }

        if (YUN_DEBUG) {
            echo "错误信息: ";
            print_r($model->getErrors());
        }

        if ($fail != null) {
            $fail($model->getErrors());
        }

        return false;
    }

}