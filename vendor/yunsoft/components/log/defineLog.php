<?php

defined('LOG_HANDLER_FILE') or define('LOG_HANDLER_FILE', 'file');
defined('LOG_HANDLER_DB') or define('LOG_HANDLER_DB', 'db');

//日志记录中数组的序列化方式
//仅对数组有效,对象无效,对象只能利用print_r()序列化成字符串
defined('LOG_ARRAY_SERIALIZATION_JSON') or define('LOG_ARRAY_SERIALIZATION_JSON', 'json');
defined('LOG_ARRAY_SERIALIZATION_STRING') or define('LOG_ARRAY_SERIALIZATION_STRING', 'string');