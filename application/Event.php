<?php


namespace app;


class Event extends \yun\console\Event
{
    public function onWorkerStart($businessWorker)
    {

    }

    public function onConnect($client_id)
    {

    }

    public function afterConnect($client_id)
    {
        return true;
    }

    public function onMessage($client_id, $recv_data)
    {

    }

    public function onClose($client_id)
    {

    }
}

