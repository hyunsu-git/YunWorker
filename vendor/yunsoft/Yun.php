<?php

require __DIR__ . '/YunBase.php';

class Yun extends YunBase
{




}

//加载组件相关的变量定义
require \yun\helpers\FileHelper::formatPath(CORE_PATH . "/components/encrypt/defineEncrypt.php");
require \yun\helpers\FileHelper::formatPath(CORE_PATH . "/components/log/defineLog.php");


