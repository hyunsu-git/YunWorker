<?php

/**
 * 无效数据的数据,直接抛出错误
 */
defined('INVALID_MESSAGE_EXCEPTION') or define('INVALID_MESSAGE_EXCEPTION','exception');
/**
 * 无效的数据直接忽略
 */
defined('INVALID_MESSAGE_NULL') or define('INVALID_MESSAGE_NULL','null');

/**
 * 行为格式错误的数据使用默认分发器处理
 */
defined('ACTION_ERROR_DEFAULT') or define('ACTION_ERROR_DEFAULT','default');
/**
 * 行为格式错误的数据默认丢弃,不作处理
 */
defined('ACTION_ERROR_NULL') or define('ACTION_ERROR_NULL','null');
/**
 * 行为格式错误的数据抛出错误
 */
defined('ACTION_ERROR_EXCEPTION') or define('ACTION_ERROR_EXCEPTION', 'exception');
