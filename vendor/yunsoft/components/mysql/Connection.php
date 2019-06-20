<?php


namespace yun\components\mysql;


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
            $this->mysql = new \Workerman\MySQL\Connection($this->host, $this->port, $this->user, $this->password, $this->dbname, $this->charset);
        }
        return $this->mysql;
    }
}