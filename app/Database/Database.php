<?php

namespace App\Database;

use App\Bootstrap\Environment;

use PDO;
use PDOException;

class Database extends Environment
{
    protected static $connection = null;
    protected static $username = "";
    protected static $password = "";
    protected static $database = "";
    protected static $port = "";
    protected static $host = "";
    protected static $dsn = "";

    public function __construct()
    {
        parent::__construct();
        self::$host = parent::env("DB_HOST");
        self::$port = parent::env("DB_PORT");
        self::$username = parent::env("DB_USERNAME");
        self::$password = parent::env("DB_PASSWORD");
        self::$database = parent::env("DB_NAME");
        self::$dsn = parent::env("DB_PDO") . ":host=" . self::$host . "; port=" . self::$port . ";dbname=" . self::$database;
    }

    public static function getConnection()
    {
        try {
            self::$connection = new PDO(self::$dsn, self::$username, self::$password);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return self::$connection;
    }

    public function __destruct()
    {
        self::$connection = null;
    }
}
