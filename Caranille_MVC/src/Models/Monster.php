<?php

require_once(__DIR__ . '/../../config/database.php');

class Monster {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllMonsters() {
        try {
            $query = "SELECT * FROM car_monsters";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching monsters: " . $e->getMessage());
        }
    }
}
