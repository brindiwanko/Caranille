<?php

require_once(__DIR__ . '/../../config/database.php');

class News {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllNews() {
        try {
            $query = "SELECT * FROM car_news ORDER BY newsId DESC LIMIT 0,4";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching news: " . $e->getMessage());
        }
    }
}
