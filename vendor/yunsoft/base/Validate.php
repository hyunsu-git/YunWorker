<?php


namespace yun\base;


use yun\exception\InvalidArgumentException;
use yun\helpers\LangHelper;

class Validate
{
    /**
     * @var array 系统内置验证规则
     * 包含一个特殊验证 convert,该方法用于将数据转换成特定类型
     */
    public static $sysRules = ['required', 'exist', 'unique', 'string', 'integer', 'int', 'number', 'email', 'compare', 'date', 'time', 'datetime', 'in', 'match', 'type', 'url', 'ip', 'mac', 'convert'];

    /**
     * 验证规则写了message字段,则使用值作为错误信息,否则使用默认错误信息
     * @param $default string 默认错误信息
     * @param $row array 验证规则,从里面提取message字段
     * @return mixed
     * @author hyunsu
     * @time 2019-06-21 10:30
     */
    private static function getMessage($default, $row)
    {
        return isset($row['message']) ? $row['message'] : $default;
    }

    /**
     * 验证属性是否有值
     * @param $value mixed 要验证的值
     * @param $name string 属性名称
     * @param array $row
     * 验证格式
     * ```
     * [[],'required','message'=>'']
     * ```
     * @return bool|mixed
     * @author hyunsu
     * @time 2019-06-21 10:14
     */
    public static function validRequired($value, $name, $row = [])
    {
        if (empty($value) || $value === '') {
            return $message = self::getMessage(LangHelper::ts('%s can not be blank.', $name), $row);
        } else {
            return true;
        }
    }

    /**
     * 验证属性值在数据库中是否存在
     * @param $value mixed 要验证的值
     * @param $name string 属性名称
     * @param array $row
     * 表名可以是'库名.表名'的形式
     * ```
     * [[],'exist','message'=>'','table'=>'db.table','field'=>'']
     * ```
     * @return bool|mixed
     * @see \backend\models
     * @see \frontend\models
     * @author hyunsu
     * @time 2019-06-21 11:03
     */
    public static function validExist($value, $name, $row = [])
    {
        if (!isset($row['table'])) {
            throw new InvalidArgumentException(LangHelper::ts(
                "Verification rule `%s` must contain `%s` field.", 'exist', 'table'));
        }
        if (!isset($row['field'])) {
            throw new InvalidArgumentException(LangHelper::ts(
                "Verification rule `%s` must contain `%s` field.", 'exist', 'field'));
        }
        $result = \Yun::getWorker()->mysql->select()->from($row['table'])->where("{$row['field']}= :field")->bindValues(array('field' => $value))->row();
        if (empty($result)) {
            return $message = self::getMessage(LangHelper::ts('%s does not exist in the database.', $name), $row);
        } else {
            return true;
        }
    }

    /**
     * 验证是否是有效的mac地址
     * @param $value mixed 要验证的值
     * @param $name string 属性名称
     * @param array $row
     * ```
     * [[],'mac','message'=>'']
     * ```
     * @return bool|mixed
     * @author hyunsu
     * @time 2019-06-21 13:56
     */
    public static function validMac($value, $name, $row = [])
    {
        $reg = "/^[A-Fa-f\d]{2}:[A-Fa-f\d]{2}:[A-Fa-f\d]{2}:[A-Fa-f\d]{2}:[A-Fa-f\d]{2}:[A-Fa-f\d]{2}$/";
        if (preg_match($reg, $value)) {
            return true;
        }else{
            return $message = self::getMessage(LangHelper::ts('%s is not a valid MAC address.', $name), $row);
        }
    }

}