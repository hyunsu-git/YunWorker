<?php


namespace yun\helpers;


use yun\base\Application;
use yun\base\Config;

class LangHelper
{
    /**
     * 对字符串进行国际化转换
     * 默认进行配置中的主语言转换,如果没有配置主语言,则直接对原始字符串格式化
     * 进行其他语种翻译
     * @param string $str 要转换的字符串,可以使用c风格格式化
     * @param mixed ...$args 格式化参数
     * @return string
     * @see tsAssign()
     * @author hyunsu
     * @time 2019-06-05 13:09
     */
    public static function ts($str, ...$args)
    {
        $lang = Config::get('language');
        //没有设置主语言直接返回
        if (!$lang) return sprintf($str, ...$args);

        $ary_lang = Application::getLanguage($lang);

        if (isset($ary_lang[$str])) {
            return sprintf($ary_lang[$str], ...$args);
        } else {
            return sprintf($str, ...$args);
        }
    }

    /**
     * 对字符串进行指定语言的国际化转换
     * 转换默认语言
     * @see ts()
     * @param $str string 要转换的字符串,可以使用c风格格式化
     * @param $lang string 要转换的语言
     * @param mixed ...$args 格式化参数
     * @return string
     * @author hyunsu
     * @time 2019-06-05 13:13
     */
    public static function tsAssign($str, $lang, ...$args)
    {
        $ary_lang = Application::getLanguage($lang);

        if (isset($ary_lang[$str])) {
            return sprintf($ary_lang[$str], ...$args);
        } else {
            return sprintf($value, ...$args);
        }
    }


    public static function loadByName($name)
    {
        $file = CORE_PATH . "languages/{$name}.php";

        $lang = [];

        if (is_file($file)) {
            $lang = require $file;
        }

        $file2 = COMMON_PATH . "languages/{$name}.php";

        if (is_file($file2)) {
            $lang = ArrayHelper::merge(
                $lang,
                require $file2
            );
        }

        return $lang;
    }
}