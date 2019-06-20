<?php


namespace yun\helpers;

defined('COMMAND_COLOR_BLACK') or define('COMMAND_COLOR_BLACK', 0);
defined('COMMAND_COLOR_RED') or define('COMMAND_COLOR_RED', 1);
defined('COMMAND_COLOR_GREEN') or define('COMMAND_COLOR_GREEN', 2);
defined('COMMAND_COLOR_YELLOW') or define('COMMAND_COLOR_YELLOW', 3);
defined('COMMAND_COLOR_BLUE') or define('COMMAND_COLOR_BLUE', 4);
defined('COMMAND_COLOR_VIOLET') or define('COMMAND_COLOR_VIOLET', 5);
defined('COMMAND_COLOR_CYAN') or define('COMMAND_COLOR_CYAN', 6);
defined('COMMAND_COLOR_WHITE') or define('COMMAND_COLOR_WHITE', 7);

class StringHelper
{
    /**
     * 该方法是php内置方法 `ucwords()` 的unicode编码安全的实现
     * @param string $string
     * @param string $encoding
     * @return string
     * @see https://secure.php.net/manual/en/function.ucwords.php
     * @since 2.0.16
     */
    public static function mb_ucwords($string, $encoding = 'UTF-8')
    {
        $words = preg_split("/\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);

        $titelized = array_map(function ($word) use ($encoding) {
            return static::mb_ucfirst($word, $encoding);
        }, $words);

        return implode(' ', $titelized);
    }

    /**
     * 在命令行中输入带有颜色的文字
     * 颜色定义为 COMMAND_COLOR_* 共8中颜色
     * @param $str string 要输出的文字
     * @param string|integer $front_color 文字颜色
     * @param string|integer $back_color 背景颜色
     * @param bool $bold 字体加粗
     * @param bool $half_light 亮度减半
     * @param bool $underline 带有下划线
     * @param bool $twinkle 字体闪烁
     * @return string
     * @author hyunsu
     * @time 2019-06-20 16:56
     */
    public static function commandColor($str, $front_color = '', $back_color = '', $bold = false, $half_light = false, $underline = false, $twinkle = false)
    {

        $command = "\033[3{$front_color};4{$back_color}";
        if ($bold) $command .= ";1";
        if ($half_light) $command .= ";2";
        if ($underline) $command .= ";4";
        if ($twinkle) $command .= ";5";
        $command .= "m{$str}\033[0m";

        return $command;
    }
}