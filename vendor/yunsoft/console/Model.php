<?php


namespace yun\console;


use GatewayWorker\Lib\Gateway;
use yun\base\Validate;
use yun\exception\InvalidArgumentException;
use yun\helpers\LangHelper;

class Model
{
    /**
     * @var bool 是否自动拆分收到的数据,填充变量
     */
    protected $auto_fill = true;

    /**
     * @var null 客户端发送的数据
     */
    protected $data = null;

    /**
     * @var \Workerman\MySQL\Connection
     * 数据库组件实例
     */
    protected $db;

    /**
     * @var \yun\components\redis\Connection
     * redis组件实例
     */
    protected $redis;

    /**
     * @var $client_id string 全局唯一的客户端id
     */
    protected $client_id;


    public $errors = [];

    /**
     * Model constructor.
     * @param null $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;

        $this->db = \Yun::getWorker()->mysql;
        $this->redis = \Yun::getWorker()->redis;

        $this->client_id = $_SERVER['GATEWAY_CLIENT_ID'];
    }

    /**
     * 验证规则,可以对类中的任何属性字段进行验证
     * 数组中的每一个元素(也是数组)作为一条验证规则
     * 不同的验证规则支持不同的扩展
     * 如 string 字符串验证,支持 tooLong和tooShort验证,integer整数验证支持 '>'和'<'验证
     * @return array
     * @see \yun\base\Validate::$sysRules 支持的验证规则
     * @author hyunsu
     * @time 2019-06-21 10:35
     */
    public function rules()
    {
        return [];
    }

    /**
     * 验证模型,验证规则写在[[rules()]]中
     * @see rules()
     * @return bool 验证全部通过返回true,有任何一个不通过返回false
     * @author hyunsu
     * @time 2019-06-21 10:31
     */
    public function validate()
    {
        $ary = $this->rules();

        //所有字段的自定义名称
        $field_name = $this->attributeLabels();

        foreach ($ary as $row) {
            if (!is_array($row)) {
                continue;
            }
            $fields = array_shift($row);
            if (is_string($fields)) {
                $fields = [$fields];
            }
            //验证规则
            $ru = array_shift($row);
            if (in_array($ru, Validate::$sysRules)) {
                $class = '\yun\base\Validate';
                //验证方法
                $fun_ru = 'valid' . ucfirst($ru);
            } else {
                throw new InvalidArgumentException(LangHelper::ts("Invalid validation rule:%s"), $ru);
            }
            foreach ($fields as $field) {
                if (!isset($this->$field)) {
                    throw new InvalidArgumentException(LangHelper::ts('Attributes that do not exist in a class:%s.', ucfirst($ru)));
                }
                $name = isset($field_name[$ru]) ? $field_name[$ru] : $ru;
                $ret = call_user_func([$class, $fun_ru], $this->$field, $ru, $row);
                if ($ret !== true) {
                    $this->errors[$field] = $ret;
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * 字段名称映射
     * 例如:['name'=>'名称','pwd'=>'password']
     * 则在出现字段名称的地方(如错误信息中),`name`字段将被替换为`名称`,`pwd`字段将被替换为`password`
     * @return array
     * @author hyunsu
     * @time 2019-06-21 10:32
     */
    public function attributeLabels()
    {
        return [];
    }

    /**
     * 获取验证规则产生的错误
     * @return array
     * @author hyunsu
     * @time 2019-06-21 10:27
     */
    public function getErrors()
    {
        return $this->errors;
    }


    public function execute()
    {

    }

    /**
     * 向客户端发送信息,默认向当前客户端发送
     * @param $msg mixed 要发送的信息
     * @param string $client 要发送的客户端,默认使用当前客户端
     * @param string $format 对数据进行格式化,默认非字符串进行json序列化
     * @return void
     * @author hyunsu
     * @time 2019-06-21 10:25
     */
    public function sendToClient($msg, $client = '', $format = 'json')
    {
        if (!$client) $client = $this->client_id;
        if ($format == 'json') {
            $msg = $this->sendJsonFomat($msg);
        }
        Gateway::sendToClient($client, $msg);
    }

    /**
     * 非字符串进行json序列化
     * @param $msg 要序列化的数据
     * @return false|string
     * @author hyunsu
     * @time 2019-06-21 10:26
     */
    protected function sendJsonFomat($msg)
    {
        return is_string($msg) ? $msg : json_encode($msg);
    }

}