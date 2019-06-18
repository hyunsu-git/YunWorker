<?php


namespace yun\dispatchers;


/**
 * Interface IDispatcher
 * 分配器接口
 * 分配器可以在配置文件中自行配置,如果不配置或者配置为false则不启用.
 * 例如
 * ```
 * 'components'=>[
 *     'dispatcher'=>false,
 *  ],
 * ```
 * 或者可以指定分配器,方法和指定其他组件一致
 * ```
 * 'components'=>[
 *      'dispatcher'=>[
 *          'class'=>'yun\dispatcher\JsonDispatcher'
 *      ]
 * ]
 * ```
 * 不启用分配器的情况下,所有消息直接转发到用户的 [[onMessage()]] 函数
 * 启用分配器后,所有消息传递到分配器,由分配器进行处理.
 * 注意:启用分配器的情况下,依然可以在 [[beforeMessage()]] 中获取到原本的数据,并且可以暂时关闭分配器
 * @package yun\dispatcher
 */
interface IDispatcher
{
    /**
     * 分发器接收信息的方法
     * 每次收到信息后,都会调用该方法
     * @return mixed
     * @author hyunsu
     * @time 2019-06-17 16:08
     */
    public function receive($message);
}