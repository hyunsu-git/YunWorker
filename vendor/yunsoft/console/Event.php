<?php


namespace yun\console;


class Event implements IEvent
{
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