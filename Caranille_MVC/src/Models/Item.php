<?php

require_once(__DIR__ . '/../../config/database.php');

class Item {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllItems() {
        try {
            $query = "SELECT * FROM car_items";
            $stmt = this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching items: " . $e->getMessage());
        }
    }
}
