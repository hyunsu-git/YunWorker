<?php


namespace yun\helpers;


use yun\Loader;

class FileHelper
{
    /**
     * 对一个目录进行格式化,
     * 格式化后的目录同意去掉了最后面的 `正反斜线`
     * @param $dir string 目录可以是别名开始的
     * @return bool|mixed|string
     */
    public static function formatDirectory($dir)
    {
        $dir = rtrim($dir, '/');
        $dir = rtrim($dir, '\\');

        if (strpos($dir, '@') === 0) {
            $dir = Loader::getAlias($dir);
        }

        return $dir;
    }

    /**
     * 对路径进行格式化,把路径中的 '/' 替换成系统目录分隔符
     * @param $path
     * @return string
     * @author hyunsu
     * @time 2019-06-11 10:49
     */
    public static function formatPath($path)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }


    /**
     * 创建日志文件,自动根据大小拆分
     * 如果日志文件不存在会创建,如果存在并且超过限制大小,会将源文件重命名并创建新的空文件
     * @param $file string  文件路径
     * @param int $max_size 文件限制大小,单位MB
     * @return bool|int
     * @author hyunsu
     * @time 2019-06-12 09:40
     */
    public static function createLogFile($file, $max_size = 5)
    {
        $file = str_replace('\\', '/', $file);

        $dir = rtrim(dirname($file), '/');

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        if (!is_file($file)) {
            return file_put_contents($file, '');
        }

        $size = filesize($file);

        if (!$size) {
            return file_put_contents($file, '');
        }

        if ($size < $max_size * 1024 * 1024) {
            return true;
        }

        $index = 0;

        $ary_file = explode('/', $file);

        $filename = $ary_file[sizeof($ary_file) - 1];

        $ary_filename = explode('.', $filename);

        //日志备份文件的起始部分
        $fm = $ary_filename[0] . '_';

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $ary_item = explode('.', $item);

            if (strpos($ary_item[0], $fm) === 0) {
                $i = intval(str_replace($fm, '', $ary_item[0]));
                if ($i > $index) {
                    $index = $i;
                }
            }
        }

        $index++;

        rename($file, "{$dir}/{$fm}{$index}" . str_replace($ary_filename[0], '', $filename));

        file_put_contents($file, '');
        
        return true;
    }
}