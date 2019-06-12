<?php


namespace yun\encrypt\components;



use yun\exception\EncryptException;
use yun\exception\SecretKeyException;
use yun\helpers\FileHelper;

/**
 * Class RSA
 * @package yun\encrypt
 *
 * openssl genrsa -out rsa_private_key.pem 1024
 * openssl pkcs8 -topk8 -inform PEM -in rsa_private_key.pem -outform PEM -nocrypt -out private_key.pem
 * openssl rsa -in rsa_private_key.pem -pubout -out rsa_public_key.pem
 * 第一条命令生成原始 RSA私钥文件 rsa_private_key.pem，
 * 第二条命令将原始 RSA私钥转换为 pkcs8格式，
 * 第三条生成RSA公钥 rsa_public_key.pem
 * 从上面看出通过私钥能生成对应的公钥，因此我们将私钥private_key.pem用在服务器端，公钥发放给Android跟iOS等前端
 *
 * 关于 RSA密钥长度、明文长度和密文长度
 * @see https://blog.csdn.net/lvxiangan/article/details/45487943
 *
 * 关于加密长度
 * openssl_private_encrypt 一次最多加密 117 个字符,这个数组取决于:
 * 对于1024位密钥长度=>要加密的最大字符数（字节）=1024/8-11（使用填充时）=117个字符（字节）。
 * 对于2048位密钥长度=>要加密的最大字符数（字节）=2048/8-11（使用填充时）=245个字符（字节）。
 * 数字11 是PKCS#1建议的padding就占用了11个字节。
 */
class RSA
{

    /**
     * @var string 私钥,可以是字符串或者文件路径,优先判断文件路径
     */
    public $private_key;

    /**
     * @var string 公钥,可以是字符串或者文件路径,优先判断文件路径
     */
    public $public_key;

    /**
     * @var int 加密的时候,数据拆分成的块大小
     * 该值模式是117,也就是密钥是1024的长度;如果密钥是用其他长度,请对应的更改这个值
     */
    public $encrypt_block_size = 117;

    /**
     * @var int 解密时候,数据拆分的块大小
     * 该值模式是128,也就是密钥是1024的长度;如果密钥是用其他长度,请对应的更改这个值
     */
    public $decrypt_block_size = 128;

    /**
     * @var bool 结果是否进行base64转码
     */
    public $result_base64 = true;


    public static function GenerateKeyFile($directory, $length = 1024, $config = [])
    {
        if (empty($config)) {
            $config = [
                "digest_alg" => "RSA-SHA1",
                "private_key_bits" => $length,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            ];
        }

        //生成私钥
        $private_key = openssl_pkey_new($config);
        //将私钥转换成字符串
        openssl_pkey_export($private_key, $str_private_key);
        //获取私钥详细信息(里面包含公钥)
        $detail = openssl_pkey_get_details($private_key);
        $str_public_key = $detail["key"];

        $directory = FileHelper::formatDirectory($directory);

        $private_file_path = $directory . "/rsa_private_key.pem";
        $public_file_path = $directory . "/rsa_public_key.pem";

        file_put_contents($private_file_path, $str_private_key);
        file_put_contents($public_file_path, $str_public_key);
    }


    /**
     * 使用私钥对数据加密,如果数据过大,会拆分成块,进行分块加密
     * @param $data string|mixed 要加密的数据,如果不是字符串,会先进行json序列化
     * @return string string  加密后的数据
     * @throws EncryptException
     * @throws SecretKeyException
     */
    public function privateEncrypt($data)
    {
        if (is_string($data)) $data = json_encode($data);

        if (is_file($this->private_key)) {
            $private_key = file_get_contents($this->private_key);
        }else{
            $private_key = $this->private_key;
        }

        $resource_private_key = openssl_pkey_get_private($private_key);

        if (!$resource_private_key) {
            throw new SecretKeyException('Private Key is invalid');
        }

        $res = '';

        $ary_data = str_split($data, $this->encrypt_block_size);

        foreach ($ary_data as $d) {
            if (openssl_private_encrypt($d, $encrypted, $resource_private_key) === false) {
                throw new EncryptException();
            }

            $res .= $encrypted;
        }

        if ($this->result_base64) {
            $res = base64_encode($res);
        }

        openssl_free_key($resource_private_key);

        return $res;
    }

    /**
     * 使用公钥进行解密
     * 会先对密文拆分成小块,然后分块解密,再组合成原文
     * @param $data string 密文
     * @return string string 解密出的原文
     * @throws DecryptException
     * @throws SecretKeyException
     */
    public function publicDecrypt($data)
    {
        if (is_file($this->public_key)) {
            $public_key = file_get_contents($this->public_key);
        }else{
            $public_key = $this->public_key;
        }

        $resource_public_key = openssl_pkey_get_public($public_key);

        if (!$resource_public_key) {
            throw new SecretKeyException('Public Key is invalid');
        }

        $res = '';

        if ($this->result_base64) {
            $data = base64_decode($data);
        }

        $data = str_split($data, $this->decrypt_block_size);

        foreach ($data as $d) {
            $decrypted = '';

            if (openssl_public_decrypt($d, $decrypted, $resource_public_key, OPENSSL_PKCS1_PADDING) === false) {
                throw new DecryptException();
            }

            $res .= $decrypted;
        }

        openssl_free_key($resource_public_key);

        return $res;
    }
}