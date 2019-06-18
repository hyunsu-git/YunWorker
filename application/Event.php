<?php


namespace app;


class Event extends \yun\console\Event
{
    public function onWorkerStart($businessWorker)
    {

    }

    public function onConnect($client_id)
    {
        var_dump($client_id);
    }

    public function afterConnect($client_id)
    {
        return true;
    }

    public function onMessage($client_id, $recv_data)
    {
        var_dump($recv_data);
    }

    public function onClose($client_id)
    {

    }
}