<?php


namespace yun\console;


class Event implements IEvent
{
    /**
     * @var bool 是否启用分配器,用户可以在任何地方手动设置为false,关闭分配器.
     * 关闭分配器后需要手动开启
     */
    public $enableDispatcher = true;

    /**
     * {@inheritdoc}
     */
    public function onWorkerStart($businessWorker)
    {
        // TODO: Implement onWorkerStart() method.
    }

    /**
     * {@inheritdoc}
     */
    public function onMessage($client_id, $recv_data)
    {
        // TODO: Implement onMessage() method.
    }

    /**
     * {@inheritdoc}
     */
    public function onClose($client_id)
    {
        // TODO: Implement onClose() method.
    }

    /**
     * {@inheritdoc}
     */
    public function onWebSocketConnect($client_id, $data)
    {
        // TODO: Implement onWebSocketConnect() method.
    }

    /**
     * {@inheritdoc}
     */
    public function onConnect($client_id)
    {
        // TODO: Implement onConnect() method.
    }

    /**
     * {@inheritdoc}
     */
    public function afterConnect($client_id)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeMessage($client_id, &$recv_data)
    {
        return true;
    }


}