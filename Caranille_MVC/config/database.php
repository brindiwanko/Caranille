<?php

class Database {
    private static $instance = null;
    private $connection;

    private $host = __DIR__ . '/caranille.db';

    private function __construct() {
        try {
            $this->connection = new PDO("sqlite:" . $this->host);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
