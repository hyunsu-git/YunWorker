<?php


namespace app;


use Yun;
use yun\components\encrypt\AES;

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

        return json_encode(["close" => 'close']);
    }

    public function onMessage($client_id, $recv_data)
    {
        var_dump($recv_data);
    }
}