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
        if (isset($data['config_json'])) {
            $query = "UPDATE etiquetas_templates SET config_json = :config WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':config', $data['config_json']);
            $stmt->bindValue(':id', (int)$data['id'], PDO::PARAM_INT);
            return $stmt->execute();
        } else {
            $query = "UPDATE etiquetas_templates SET ancho = :ancho, alto = :alto WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':ancho', $data['ancho']);
            $stmt->bindValue(':alto', $data['alto']);
            $stmt->bindValue(':id', (int)$data['id'], PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    public function getById($id) {
        $query = "SELECT * FROM etiquetas_templates WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
