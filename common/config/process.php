<?php
/**
 * 设置business进程相关参数
 * 设置采用数字下标,下标表示business进程的id
 * 例如,设置0号进程和3号进程
 * ```
 * [
 *      [name=>'p0'],
 *      3=>[name='p3']
 * ]
 * ```
 * 没有设置的进程采用默认值
 * 可以设置的内容包括:
 * @param string name 进程名称
 * @param boolean supportConnection 当前进程是否支持gateway进程分配客户端,默认为true
 *                  设置为false表示不允许分配.可以独立出一个或几个进程专门用于运行定时器或处理其他业务
 *
 * 注意:如果支持客户端连接的进程数小于1,会造成客户端无法连接
 */
return array(
    array(
        'name' => 'timer',
        'supportConnection' => false,
    )


);