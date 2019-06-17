<?php


namespace app;


use GatewayWorker\Lib\Gateway;

class Event extends \yun\console\Event
{
    public function onWorkerStart($businessWorker)
    {

    }

    public function onConnect($client_id)
    {
        var_dump($client_id);

        Gateway::sendToAll("aaaa");
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
        var_dump($client_id . "@@@");
    }
}