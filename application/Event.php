<?php


namespace app;


use yun\helpers\StringHelper;

class Event extends \yun\console\Event
{
    public function onWorkerStart($businessWorker)
    {

    }

    public function onConnect($client_id)
    {
        echo $client_id . ' ' . StringHelper::commandColor("is connected!", COMMAND_COLOR_GREEN, '', true) . PHP_EOL;
    }

    public function afterConnect($client_id)
    {
        return true;
    }

    public function onMessage($client_id, $recv_data)
    {
        echo $client_id . 'send message:' . PHP_EOL;
        echo "\t" . $recv_data . PHP_EOL;
    }

    public function onClose($client_id)
    {
        echo $client_id . ' ' . StringHelper::commandColor("is closed!", COMMAND_COLOR_RED, '', true) . PHP_EOL;
    }
}

