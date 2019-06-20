<?php


namespace app;


use yun\exception\Exception;

class Event extends \yun\console\Event
{
    public function onWorkerStart($businessWorker)
    {
        echo 1 / 0;
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

