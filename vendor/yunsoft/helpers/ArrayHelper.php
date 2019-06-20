<?php


namespace yun\helpers;

class ArrayHelper
{
    /**
     * 将一个一维数组递归格式化成多维数组,一维数组的每个元素作为上一个元素的键
     * 比如:
     * ```php
     * $ary1 = ['a','b','c','d']
     * $ary2 = formatRecursiveArrayByArray($ary1,'value')
     *
     * //输入$ary2为
     * [
     *  'a'=>[
     *      'b'=>[
     *          'c'=>[
     *                'd'=>'value'
     *              ]
     *           ]
     *      ]
     * ]
     *
     * ```
     * @param $ary array 需要格式化的一维数组
     * @param string $value 最终元素的值
     * @return array|string
     * @author hyunsu
     * @time 2019-06-03 16:09
     */
    public static function formatRecursiveArrayByArray($ary, $value = '')
    {
        $res = array();
        if (isset($ary[0])) {
            $res[array_shift($ary)] = self::formatRecursiveArrayByArray($ary,$value);
            return $res;
        }else{
            return $value;
        }
    }

    /**
     * 将两个或两个以上的数组进行合并
     * 同一个键,如果值是数组,则递归合并,如果值的字符串则后面的会替换前面的值
     * @param $a
     * @param $b
     * @return mixed
     * @author hyunsu
     * @time 2019-06-03 17:49
     */
    public static function merge($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {
                if (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = static::merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * 将驼峰式的名称转换成以空白分隔的名称
     * 例如 'PostTag' 将被转换成 'Post Tag'
     * @param string $name 要转换的名称
     * @param bool $ucwords 是否将每一个首字母大写
     * @return string
     */
    public static function camel2words($name, $ucwords = true)
    {
        $label = mb_strtolower(trim(str_replace([
            '-',
            '_',
            '.',
        ], ' ', preg_replace('/(?<!\p{Lu})(\p{Lu})|(\p{Lu})(?=\p{Ll})/u', ' \0', $name))),'UTF-8');

        return $ucwords ? StringHelper::mb_ucwords($label) : $label;
    }
}