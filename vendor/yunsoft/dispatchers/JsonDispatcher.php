<?php


namespace yun\dispatchers;


use function PHPSTORM_META\type;
use yun\exception\InvalidArgumentException;
use yun\helpers\LangHelper;

/**
 * Class JsonDispatcher
 * JSON格式数据分发器
 * 数据格式为
 * ```
 * [
 *      'act'=>['controller','action'],
 *      'data'=> mixed
 * ]
 * ```
 * @package yun\dispatchers
 */
class JsonDispatcher extends Dispatcher
{
    /**
     * @var string JSON对象中表示行为的字段名称
     * 内容必须是一维数组,两个元素分别是controller和action
     */
    public $actField = 'act';

    /**
     * @var mixed JSON对象中表示具体内容的字段名称
     */
    public $dataField = 'data';

    /**
     * {@inheritdoc}
     */
    public function receive($message)
    {
        if (!is_string($message)) {
            if ($this->invalidMessageMode === INVALID_MESSAGE_EXCEPTION) {
                throw new InvalidArgumentException(LangHelper::ts('yun\dispatcher\JsonDispathcer::receive() expects a string parameter,but gives a %s', type($message)));
            } else {
                return;
            }
        }

        try {
            $message = json_decode($message, true);
        } catch (\Exception $e) {
            if ($this->invalidMessageMode === INVALID_MESSAGE_EXCEPTION) {
                throw new InvalidArgumentException(LangHelper::ts('JSON decode failure,content is: %s', $message));
            } else {
                return;
            }
        }

        $data = null;
        if (isset($message[$this->dataField])) {
            $data = $message[$this->dataField];
        }

        if (!isset($message[$this->actField])) {
            $this->errorAction($data);
        } else {
            $ary_act = $message[$this->actField];
            if (sizeof($ary_act) < 2) {
                $this->errorAction($data);
            } else {
                $this->play($ary_act[0], $ary_act[1], $data);
            }
        }
    }


    /**
     * action错误的处理方式
     * @param $data
     * @return void
     * @author hyunsu
     * @time 2019-06-17 18:02
     */
    private function errorAction($data)
    {
        if ($this->actionErrorMode === ACTION_ERROR_EXCEPTION) {
            throw new InvalidArgumentException(LangHelper::ts('Invalid message,no field %s exists.', $this->actField));
        } else if ($this->actionErrorMode === ACTION_ERROR_DEFAULT) {
            $this->defaultPlay($data);
        } else if ($this->actionErrorMode === ACTION_ERROR_NULL) {
            return;
        }
    }
}