<?php

require_once "config/config.php";

class DataBase
{

    private static $db = null;
    private $config;
    private $mysqli;

    /**
     * DataBase constructor.
     */
    private function  __construct()
    {
        $this->config = new Config();
        $this->mysqli = new mysqli($this->config->host, $this->config->user, $this->config->pass, $this->config->dbName);
        $this->mysqli->query("SET NAME 'utf8'");
    }

    /**
     * @return DataBase
     */
    public static function getDB()
    {
        return empty(self::$db) ? self::$db = new DataBase() : self::$db;
    }

    /**
     * @return mysqli
     */
    public function getMysqli()
    {
        return $this->mysqli;
    }

    public function __destruct()
    {
        if($this->mysqli) $this->mysqli->close();
    }
}