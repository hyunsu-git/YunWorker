<?php


namespace yun\helpers;



class CacheHelper
{
    /**
     * 从hash数据中获取单个键
     * @param $key
     * @param $field
     * @return mixed
     * @author hyunsu
     * @time 2019-06-19 15:03
     */
    public static function getHashOne($key, $field)
    {
        $redis = \Yun::getWorker()->redis;

        return $redis->hget($key, $field);
    }

    /**
     * 获取hash中的全部数据
     * @param $key
     * @param $field array 如果数组有值,则只获取数组中的键.如果为空数组,则获取全部键
     * @return array
     * @author hyunsu
     * @time 2019-06-19 15:57
     */
    public static function getHashAll($key, $field = [])
    {
        $redis = \Yun::getWorker()->redis;

        if (empty($field)) {

            $ary = $redis->hgetall($key);

            $ret = [];

            if ($ary) {

                for ($i = 0; $i < sizeof($ary); $i = $i + 2) {

                    $ret[$ary[$i]] = $ary[$i + 1];

                }
            }

            return $ret;

        } else {
            $ary = $redis->hmget($key, ...$field);

            $ret = [];
            foreach ($field as $key => $item) {
                $ret[$item] = $ary[$key];
            }

            return $ret;
        }
    }
}