<?php


namespace yun\base;

/**
 * Class Loader
 * 核心加载器,负责自动加载文件
 * 加载顺序在composer之后,也就是composer能找到的会优先使用composer的加载器
 * @package yun
 */
class Loader
{
    /**
     * @var array Yun框架自动加载用的数组映射
     * 数组的键是类名(没有前面的反斜线)
     * 数组的值是类文件的路径或者别名
     */
    public static $classMap = [];

    public static $aliases = [];


    public static function autoload($className)
    {
        if (isset(self::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile);
            }
            include $classFile;
            return;
        }

        //优先在核心目录寻找
        $classFile = str_replace('\\', DS, $className) . '.php';
        if (strpos($classFile, 'yun') === 0) {
            $classFile = substr_replace($classFile, 'yunsoft', 0, 3);
        }
        if (is_file(VENDOR_PATH . $classFile)) {
            include VENDOR_PATH . $classFile;
            return;
        }

        //转换成别名查找
        $classFile = '@' . $className . '.php';
        $classFile = self::getAlias($classFile);
        $classFile = str_replace('\\', DS , $classFile);
        if (is_file($classFile)) {
            include $classFile;
            return;
        }
    }

    /**
     * 注册一个别名
     * 别名应该以@开头(没有@符号会自动追加)
     * 也就是 setAlias("app"),但是 getAlias("app") 是获取不到值的,应该使用 getAlias("@app") 获取
     * @param $alias
     * @param $path
     * @return void
     * @author hyunsu
     * @time 2019-06-03 16:21
     */
    public static function setAlias($alias,$path)
    {
        $alias = str_replace('/', '\\', $alias);

        if (strpos($alias, '@') !== 0) {
            $alias = '@' . $alias;
        }

        $alias = rtrim($alias, '\\');

        if(isset(self::$aliases[$alias])) return;

        self::$aliases[$alias] = $path;
    }

    /**
     * 将别名进行转换,如果没有设置别名会返回false
     * 优先进行长匹配, 例如 同时设置了 '@app\aa'='aadict' 和 '@app'='app' 那么 '@app\aa\bb' 会转换为 'aadict\bb'
     * @param $alias string
     * @return bool|mixed|string
     * @author hyunsu
     * @time 2019-06-03 17:32
     */
    public static function getAlias($alias)
    {
        if (strpos($alias, '@') !== 0) {
            $alias = '@' . $alias;
        }

        if(isset(self::$aliases[$alias])) return self::$aliases[$alias];

        $left = $alias;

        $right = '';

        while (true) {

            $pos = strrpos($left, '\\');

            if($pos === false || $pos === 0) return false;

            $left = substr($left, 0, $pos);

            $right = substr($alias, $pos);

            if (isset(self::$aliases[$left])) {
                return self::$aliases[$left] . $right;
            }
        }

        return false;
    }

}

Loader::setAlias("@yun", rtrim(CORE_PATH, DIRECTORY_SEPARATOR));

require VENDOR_PATH . 'autoload.php';
spl_autoload_register('yun\\base\\Loader::autoload', true, false);
