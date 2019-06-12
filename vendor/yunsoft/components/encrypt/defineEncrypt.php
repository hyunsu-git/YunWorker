<?php


/**
 * AES加密使用的扩展
 */
defined('AES_ENCRYPT_TYPE_AUTO') or define('AES_ENCRYPT_TYPE_AUTO', 'auto');
defined('AES_ENCRYPT_TYPE_MCRYPT') or define('AES_ENCRYPT_TYPE_MCRYPT', 'mcrypt');
defined('AES_ENCRYPT_TYPE_OPENSSL') or define('AES_ENCRYPT_TYPE_OPENSSL', 'openssl');

/**
 * 结果不进行加密
 */
defined('RESULT_ENCRYPT_NO') or define('RESULT_ENCRYPT_NO', 0);
/**
 * 对结果进行AES加密
 */
defined('RESULT_ENCRYPT_AES') or define('RESULT_ENCRYPT_AES', 1);
/**
 * 对结果进行RSA加密
 */
defined('RESULT_ENCRYPT_RSA') or define('RESULT_ENCRYPT_RSA', 2);
/**
 * 对结果进行AES加密,并且对key和iv进行RSA加密
 */
defined('RESULT_ENCRYPT_RSA_AES') or define('RESULT_ENCRYPT_RSA_AES', 3);

