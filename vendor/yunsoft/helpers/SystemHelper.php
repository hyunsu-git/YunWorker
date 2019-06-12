<?php


namespace yun\helpers;


class SystemHelper
{

    /**
     * 大小单位转换
     * @param $size string|int|double 如果是带有标准的单位 则按当前单位计算，否则，认为传入的是以字节为单位
     * @param string $new_unit 转换成的 单位，默认是字节
     * @return float|int 转换出的大小（不带单位）
     * @author hyunsu
     * @time 2019-06-11 15:20
     */
    public static function convert_size($size, $new_unit = 'B')
    {
        $size = strtoupper($size);

        $size_b = doubleval($size);

        if (stristr($size, 'TB') || stristr($size, 'T')) {

            $s = $size_b * 1024 * 1024 * 1024 * 1024;

        } else if (stristr($size, 'GB') || stristr($size, 'G')) {

            $s = $size_b * 1024 * 1024 * 1024;

        } else if (stristr($size, 'MB') || stristr($size, 'M')) {

            $s = $size_b * 1024 * 1024;

        } else if (stristr($size, 'KB') || stristr($size, 'K')) {

            $s = $size_b * 1024;

        } else {

            $s = $size_b;
        }

        $unit = strtoupper($new_unit);

        switch ($unit) {

            case 'TB':
            case 'T':
                return $s / (1024 * 1024 * 1024 * 1024);
                break;

            case 'GB':
            case 'G':
                return $s / (1024 * 1024 * 1024);
                break;

            case 'MB':
            case 'M':
                return $s / (1024 * 1024);
                break;

            case 'KB':
            case 'K':
                return $s / 1024;
                break;

            default:
                return $s;

        }
    }

    /**
     * 转换成小于1024的合适单位
     * @param $size string|int 可以带有单位,可以不带(作为字节)
     * @param int $precision 保留小数位数,默认2位
     * @return string 转换结果（带有单位）
     * @author hyunsu
     * @time 2019-06-11 15:22
     */
    public static function convert_suitable_unit($size, $precision = 2)
    {

        if (is_string($size)) {
            $size = self::_convert_size($size);
        }

        if ($size < 1024) {
            return round($size, 2) . "B";
        }

        $size /= 1024;

        if ($size < 1024) {
            return round($size, 2) . "KB";
        }

        $size /= 1024;

        if ($size < 1024) {
            return round($size, 2) . "MB";
        }

        $size /= 1024;

        if ($size < 1024) {
            return round($size, 2) . "GB";
        }

        $size /= 1024;

        return round($size, 2) . 'TB';
    }


    /**
     * 返回当前分配给PHP脚本的内存量
     * @param bool $format 是否格式化
     * @return int|string 不格式化则返回原始字节,格式化则返回最适合的字符串大小
     */
    public static function memory_get_usage($format = true)
    {
        if (function_exists('memory_get_usage')) {
            $value = memory_get_usage();
            return $format ? self::convert_suitable_unit($value) : $value;
        } else {
            return 0;
        }
    }

    /**
     * 返回内存使用峰值
     * @param bool $format 是否格式化
     * @return int|string 不格式化则返回原始字节,格式化则返回最适合的字符串大小
     */
    public static function memory_get_peak_usage($format = true)
    {
        if (function_exists('memory_get_peak_usage')) {
            $value = memory_get_peak_usage();
            return $format ? self::convert_suitable_unit($value) : $value;
        } else {
            return 0;
        }
    }
}