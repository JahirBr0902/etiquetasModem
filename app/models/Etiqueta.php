<?php

require_once __DIR__ . '/Model.php';

class Etiqueta extends Model {
    
    public function getAll() {
        $query = "SELECT * FROM etiquetas ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO etiquetas (nombre, modelo_modem, descripcion) VALUES (:nombre, :modelo, :desc)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':modelo', $data['modelo']);
        $stmt->bindParam(':desc', $data['descripcion']);
        
        return $stmt->execute();
    }
}
