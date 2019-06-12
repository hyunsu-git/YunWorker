<?php


namespace yun\helpers;


class RandHelper
{
    /**
     * 随机生成一串指定长度字符串
     * @param $_len int 字符串的长度
     * @return bool|string
     */
    public static function random($_len)
    {
        $len = intval($_len) <= 0 ? 32 : intval($_len);

        //mcrypt_create_iv 函数在7.1.0 版本中已经被废弃
        if (version_compare(PHP_VERSION, "7.1.0", ">=")) {

            $iv = random_bytes($len);

            $r = bin2hex($iv);

        } else {

            $r = '';

            $max = ceil($len / 32);

            for ($i = 0; $i < $max; $i++) {
                $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
                $iv = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
                $r .= strval(bin2hex($iv));
            }

        }

        return substr($r, 0, $len);
    }
}