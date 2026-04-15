<?php

require_once __DIR__ . '/Model.php';

class EtiquetaTemplate extends Model {
    
    public function getAll() {
        $query = "SELECT * FROM etiquetas_templates ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO etiquetas_templates (nombre, ancho, alto, config_json) 
                  VALUES (:nombre, :ancho, :alto, :config)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nombre', $data['nombre']);
        $stmt->bindValue(':ancho', $data['ancho']);
        $stmt->bindValue(':alto', $data['alto']);
        $stmt->bindValue(':config', $data['config_json'] ?? '[]');
        return $stmt->execute();
    }

    public function update($data) {
        $fields = [];
        if (isset($data['nombre'])) $fields[] = "nombre = :nombre";
        if (isset($data['ancho'])) $fields[] = "ancho = :ancho";
        if (isset($data['alto'])) $fields[] = "alto = :alto";
        if (isset($data['config_json'])) $fields[] = "config_json = :config";
        
        if (empty($fields)) return false;

        $query = "UPDATE etiquetas_templates SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindValue(':id', (int)$data['id'], PDO::PARAM_INT);
        if (isset($data['nombre'])) $stmt->bindValue(':nombre', $data['nombre']);
        if (isset($data['ancho'])) $stmt->bindValue(':ancho', $data['ancho']);
        if (isset($data['alto'])) $stmt->bindValue(':alto', $data['alto']);
        if (isset($data['config_json'])) $stmt->bindValue(':config', $data['config_json']);
        
        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT * FROM etiquetas_templates WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
