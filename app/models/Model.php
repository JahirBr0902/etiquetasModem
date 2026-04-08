<?php

require_once __DIR__ . '/../config/db.php';

class Model {
    protected $db;
    protected $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }
}
