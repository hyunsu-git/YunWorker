<?php


namespace yun\components\mysql;


use yun\helpers\StringHelper;

class Connection
{
    public $host = 'localhost';

    public $port = 3306;

    public $user = 'root';

    public $password = '';

    public $dbname;

    public $charset = 'utf8';

    /**
     * @var \Workerman\MySQL\Connection
     */
    private $mysql = null;

    /**
     * @return \Workerman\MySQL\Connection
     */
    public function getMysql()
    {
        if ($this->mysql === null) {
            try {
                $this->mysql = new \Workerman\MySQL\Connection($this->host, $this->port, $this->user, $this->password, $this->dbname, $this->charset);
            } catch (\Exception $exception) {
                echo StringHelper::commandColor("数据库连接失败!!",COMMAND_COLOR_RED) . PHP_EOL;
            }
        }
        return $this->mysql;
    }
}