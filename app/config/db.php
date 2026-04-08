<?php

class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $env = parse_ini_file(__DIR__ . '/../../.env');
        $this->host = $env['DB_HOST'] ?? 'localhost';
        $this->port = $env['DB_PORT'] ?? '5432';
        $this->db_name = $env['DB_NAME'] ?? 'etiquetas_modem';
        $this->username = $env['DB_USER'] ?? 'postgres';
        $this->password = $env['DB_PASS'] ?? '';
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET client_encoding TO 'UTF8'");
        } catch(PDOException $exception) {
            // No hacemos echo aquí para no romper el JSON de la API
            throw new Exception("Error de conexión a la base de datos: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
