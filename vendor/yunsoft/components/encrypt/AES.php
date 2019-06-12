<?php


namespace yun\components\encrypt;

use yun\exception\InvalidConfigException;
use yun\helpers\RandHelper;

class AES
{
    /**
     * @var string 加密用的key
     */
    public $key;

    /**
     * @var string 非 NULL 的初始化向量。
     * 初始向量只是为了给加密算法提供一个可用的种子， 所以它不需要安全保护， 你甚至可以随同密文一起发布初始向量也不会对安全性带来影响。
     * 加密过程中,优先采用函数参数列表中的iv,如果为空采用类中的iv,如果为空则随机生成
     * @see generateIv()
     */
    public $iv;

    /**
     * @var bool 加密中使用原始的key还是编码处理过的key
     * 在严格的AES中，加密的密钥必须与字符串算法长度一至，（MCRYPT_RIJNDAEL_128、MCRYPT_RIJNDAEL_192、MCRYPT_RIJNDAEL_256）
     * 也就是128位/8=16字节=16字符
     * 192位/8=24字节=24字符
     * 256位/8=32字节=32字符
     * key的编码使用 [[hex2bin(md5($key))]] 的方式,最终得到16位
     */
    public $encode_key = false;

    /**
     * @var bool 是否对结果进行base64转码
     */
    public $result_base64 = false;

    /**
     * @var string 采用哪种加密方式
     * 可选值 mcrypt 使用mcrypt扩展中的函数进行加密
     *       openssl 使用openssl扩展中的函数进行加密
     *       auto 自动判断,如果php的版本大于7.1.0采用openssl,否则采用mcrypt
     */
    public $encrypt_type = AES_ENCRYPT_TYPE_AUTO;

    /**
     * @var string mcrypt扩展加密使用 MCRYPT_ciphername 常量中的一个，或者是字符串值的算法名称。
     */
    public $mcrypt_cipher = MCRYPT_RIJNDAEL_128;

    /**
     * @var string mcrypt扩展加密使用 MCRYPT_MODE_modename 常量中的一个，或以下字符串中的一个："ecb"，"cbc"，"cfb"，"ofb"，"nofb" 和 "stream"。
     */
    public $mcrypt_mode = MCRYPT_MODE_CBC;

    /**
     * @var string openssl扩展加密使用 密码学方式。openssl_get_cipher_methods() 可获取有效密码方式列表。
     *
     * 取值范围包括:
     * (0) AES-128-CBC
     * (1) AES-128-CBC-HMAC-SHA1
     * (2) AES-128-CFB
     * (3) AES-128-CFB1
     * (4) AES-128-CFB8
     * (5) AES-128-CTR
     * (6) AES-128-ECB
     * (7) AES-128-OFB
     * (8) AES-128-XTS
     * (9) AES-192-CBC
     * (10) AES-192-CFB
     * (11) AES-192-CFB1
     * (12) AES-192-CFB8
     * (13) AES-192-CTR
     * (14) AES-192-ECB
     * (15) AES-192-OFB
     * (16) AES-256-CBC
     * (17) AES-256-CBC-HMAC-SHA1
     * (18) AES-256-CFB
     * (19) AES-256-CFB1
     * (20) AES-256-CFB8
     * (21) AES-256-CTR
     * (22) AES-256-ECB
     * (23) AES-256-OFB
     * (24) AES-256-XTS
     * (25) BF-CBC
     * (26) BF-CFB
     * (27) BF-ECB
     * (28) BF-OFB
     * (29) CAMELLIA-128-CBC
     * (30) CAMELLIA-128-CFB
     * (31) CAMELLIA-128-CFB1
     * (32) CAMELLIA-128-CFB8
     * (33) CAMELLIA-128-ECB
     * (34) CAMELLIA-128-OFB
     * (35) CAMELLIA-192-CBC
     * (36) CAMELLIA-192-CFB
     * (37) CAMELLIA-192-CFB1
     * (38) CAMELLIA-192-CFB8
     * (39) CAMELLIA-192-ECB
     * (40) CAMELLIA-192-OFB
     * (41) CAMELLIA-256-CBC
     * (42) CAMELLIA-256-CFB
     * (43) CAMELLIA-256-CFB1
     * (44) CAMELLIA-256-CFB8
     * (45) CAMELLIA-256-ECB
     * (46) CAMELLIA-256-OFB
     * (47) CAST5-CBC
     * (48) CAST5-CFB
     * (49) CAST5-ECB
     * (50) CAST5-OFB
     * (51) DES-CBC
     * (52) DES-CFB
     * (53) DES-CFB1
     * (54) DES-CFB8
     * (55) DES-ECB
     * (56) DES-EDE
     * (57) DES-EDE-CBC
     * (58) DES-EDE-CFB
     * (59) DES-EDE-OFB
     * (60) DES-EDE3
     * (61) DES-EDE3-CBC
     * (62) DES-EDE3-CFB
     * (63) DES-EDE3-CFB1
     * (64) DES-EDE3-CFB8
     * (65) DES-EDE3-OFB
     * (66) DES-OFB
     * (67) DESX-CBC
     * (68) IDEA-CBC
     * (69) IDEA-CFB
     * (70) IDEA-ECB
     * (71) IDEA-OFB
     * (72) RC2-40-CBC
     * (73) RC2-64-CBC
     * (74) RC2-CBC
     * (75) RC2-CFB
     * (76) RC2-ECB
     * (77) RC2-OFB
     * (78) RC4
     * (79) RC4-40
     * (80) RC4-HMAC-MD5
     * (81) SEED-CBC
     * (82) SEED-CFB
     * (83) SEED-ECB
     * (84) SEED-OFB
     * (85) aes-128-cbc
     * (86) aes-128-cbc-hmac-sha1
     * (87) aes-128-ccm
     * (88) aes-128-cfb
     * (89) aes-128-cfb1
     * (90) aes-128-cfb8
     * (91) aes-128-ctr
     * (92) aes-128-ecb
     * (93) aes-128-gcm
     * (94) aes-128-ofb
     * (95) aes-128-xts
     * (96) aes-192-cbc
     * (97) aes-192-ccm
     * (98) aes-192-cfb
     * (99) aes-192-cfb1
     * (100) aes-192-cfb8
     * (101) aes-192-ctr
     * (102) aes-192-ecb
     * (103) aes-192-gcm
     * (104) aes-192-ofb
     * (105) aes-256-cbc
     * (106) aes-256-cbc-hmac-sha1
     * (107) aes-256-ccm
     * (108) aes-256-cfb
     * (109) aes-256-cfb1
     * (110) aes-256-cfb8
     * (111) aes-256-ctr
     * (112) aes-256-ecb
     * (113) aes-256-gcm
     * (114) aes-256-ofb
     * (115) aes-256-xts
     * (116) bf-cbc
     * (117) bf-cfb
     * (118) bf-ecb
     * (119) bf-ofb
     * (120) camellia-128-cbc
     * (121) camellia-128-cfb
     * (122) camellia-128-cfb1
     * (123) camellia-128-cfb8
     * (124) camellia-128-ecb
     * (125) camellia-128-ofb
     * (126) camellia-192-cbc
     * (127) camellia-192-cfb
     * (128) camellia-192-cfb1
     * (129) camellia-192-cfb8
     * (130) camellia-192-ecb
     * (131) camellia-192-ofb
     * (132) camellia-256-cbc
     * (133) camellia-256-cfb
     * (134) camellia-256-cfb1
     * (135) camellia-256-cfb8
     * (136) camellia-256-ecb
     * (137) camellia-256-ofb
     * (138) cast5-cbc
     * (139) cast5-cfb
     * (140) cast5-ecb
     * (141) cast5-ofb
     * (142) des-cbc
     * (143) des-cfb
     * (144) des-cfb1
     * (145) des-cfb8
     * (146) des-ecb
     * (147) des-ede
     * (148) des-ede-cbc
     * (149) des-ede-cfb
     * (150) des-ede-ofb
     * (151) des-ede3
     * (152) des-ede3-cbc
     * (153) des-ede3-cfb
     * (154) des-ede3-cfb1
     * (155) des-ede3-cfb8
     * (156) des-ede3-ofb
     * (157) des-ofb
     * (158) desx-cbc
     * (159) id-aes128-CCM
     * (160) id-aes128-GCM
     * (161) id-aes128-wrap
     * (162) id-aes192-CCM
     * (163) id-aes192-GCM
     * (164) id-aes192-wrap
     * (165) id-aes256-CCM
     * (166) id-aes256-GCM
     * (167) id-aes256-wrap
     * (168) id-smime-alg-CMS3DESwrap
     * (169) idea-cbc
     * (170) idea-cfb
     * (171) idea-ecb
     * (172) idea-ofb
     * (173) rc2-40-cbc
     * (174) rc2-64-cbc
     * (175) rc2-cbc
     * (176) rc2-cfb
     * (177) rc2-ecb
     * (178) rc2-ofb
     * (179) rc4
     * (180) rc4-40
     * (181) rc4-hmac-md5
     * (182) seed-cbc
     * (183) seed-cfb
     * (184) seed-ecb
     * (185) seed-ofb
     */
    public $ssl_method = 'AES-128-CBC';

    /**
     * @var string 如果采用随机iv,该属性保存每次生成的iv
     * @see getPrevIv()
     */
    private $temp_iv = '';

    public function __construct($key='',$iv='')
    {
        $this->key = $key;

        $this->iv = $iv;
    }


    /**
     * 为了多语言兼容,在加密前将原文进行分组
     * 关于补齐的扩展资料:
     * 某些加密算法要求明文需要按一定长度对齐，叫做块大小(BlockSize)，比如16字节，那么对于一段任意的数据，加密前需要对最后一个块填充到16 字节，解密后需要删除掉填充的数据。
     * ZeroPadding，数据长度不对齐时使用0填充，否则不填充。
     * PKCS7Padding，假设数据长度需要填充n(n>0)个字节才对齐，那么填充n个字节，每个字节都是n;如果数据本身就已经对齐了，则填充一块长度为块大小的数据，每个字节都是块大小。
     * PKCS5Padding，PKCS7Padding的子集，块大小固定为8字节。
     *
     * PKCS#5在填充方面，是PKCS#7的一个子集：
     * PKCS#5只是对于8字节（BlockSize=8）进行填充，填充内容为0x01-0x08；
     * 但是PKCS#7不仅仅是对8字节填充，其BlockSize范围是1-255字节。
     * 所以，PKCS#5可以向上转换为PKCS#7，但是PKCS#7不一定可以转换到PKCS#5（用PKCS#7填充加密的密文，用PKCS#5解出来是错误的）。
     *
     * @param $text
     * @param $blocksize
     * @return string
     */
    function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * 删除加密前的补齐数据
     * 该方法应该在解密后调用
     * 补齐方法和删除方法均为官方提供的demo
     * @param $text
     * @return bool|string
     */
    function pkcs5_unpad($text)
    {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) return false;
        return substr($text, 0, -1 * $pad);
    }

    /**
     * 对数据进行aes加密
     * 要加密数据的类型如果不是 string ,会进行json序列化然后加密
     * 加密采用的扩展由 [[encrypt_type]] 属性决定
     * @param $str string|mixed 要加密的数据
     * @param $key string 加密用的key
     * @param string $iv 加密用的iv
     * @return string
     */
    function encrypt($str, $key = '', $iv = '')
    {
        if (!is_string($str)) $str = json_encode($str);

        if (!$key) $key = $this->key;

        if (!$key) {
            throw new InvalidConfigException('The KEY can not be empty in encryption');
        }

        if ($this->encode_key) {
            $key = hex2bin(md5($key));
        }

        if ($this->encrypt_type == AES_ENCRYPT_TYPE_MCRYPT) {
            $res = $this->encrpytMcrypt($str, $key, $iv);
        } else if ($this->encrypt_type == AES_ENCRYPT_TYPE_OPENSSL) {
            $res = $this->encryptSsl($str, $key, $iv);
        } else {
            if (version_compare(PHP_VERSION, '7.1.0', '<=')) {
                $res = $this->encrpytMcrypt($str, $key, $iv);
            } else {
                $res = $this->encryptSsl($str, $key, $iv);
            }
        }

        if ($this->result_base64) {
            $res = base64_encode($res);
        }

        return $res;
    }

    /**
     * 生成iv,优先采用函数参数列表中的iv,如果为空采用类中的iv,如果为空则随机生成
     * @param $iv string 加密函数传递的iv
     * @return bool|string
     */
    public function generateIv($iv)
    {
        if ($iv) return $iv;
        if ($this->iv) return $this->iv;
        if ($this->encrypt_type == AES_ENCRYPT_TYPE_MCRYPT) {
            return mcrypt_create_iv(mcrypt_get_iv_size($this->mcrypt_cipher, $this->mcrypt_cipher), MCRYPT_RAND);
        } else {
            $len = openssl_cipher_iv_length($this->ssl_method);
            return RandHelper::random($len);
        }
    }

    /**
     * 采用mcrypt扩展对数据加密
     * @param $str
     * @param $key
     * @param string $iv
     * @return string
     */
    private function encrpytMcrypt($str, $key, $iv = '')
    {
        $iv = $this->generateIv($iv);

        $res = mcrypt_encrypt(
            $this->mcrypt_cipher, $key,
            $this->pkcs5_pad($str, mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
            $this->mcrypt_mode, $iv);

        return $res;
    }

    /**
     * 采用openssl扩展对数据加密
     * @param $str
     * @param $key
     * @param string $iv
     * @return string
     */
    private function encryptSsl($str, $key, $iv = '')
    {
        $res = openssl_encrypt($str, $this->ssl_method, $key, OPENSSL_RAW_DATA, $iv);
        return $res;
    }

    /**
     * 获取上一次随机生成的iv
     * 随机生成的iv保留到下次使用加密函数之前
     * @return string
     */
    public function getPrevIv()
    {
        return $this->temp_iv;
    }
}