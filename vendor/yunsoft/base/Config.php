<?php


namespace yun\base;


use yun\exception\InvalidConfigException;
use yun\helpers\ArrayHelper;

class Config
{

    /**
     * 获取配置,获取到的配置是合并后的结果,也就是用户的配置信息会覆盖系统配置
     * 获取不到结果根据配置触发 \yun\exception\InvalidConfigException 异常
     * @param $key string 配置的键,可以是 aa.bb.cc的形式
     * @param bool $exception 找不到配置是否抛出异常
     * @return array|null 配置数据
     * @author hyunsu
     * @time 2019-06-11 11:37
     */
    public static function get($key, $exception = false)
    {
        $config = Application::$config;

        $ary_key = explode('.', $key);

        foreach ($ary_key as $k) {
            if (isset($config[$k])) {
                $config = $config[$k];
            } else {
                $config = null;
            }
        }

        if ($config === null && $exception) {
            throw new InvalidConfigException($key);
        }

        return $config;
    }
}