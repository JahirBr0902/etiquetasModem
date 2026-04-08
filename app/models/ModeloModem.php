<?php

require_once __DIR__ . '/Model.php';

class ModeloModem extends Model {
    
    public function getAll() {
        $query = "SELECT m.*, tp.nombre as template_primario, ts.nombre as template_secundario 
                  FROM modelos_modem m
                  LEFT JOIN etiquetas_templates tp ON m.etiqueta_primaria_id = tp.id
                  LEFT JOIN etiquetas_templates ts ON m.etiqueta_secundaria_id = ts.id
                  ORDER BY m.nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO modelos_modem (nombre, cant_etiquetas, etiqueta_primaria_id, etiqueta_secundaria_id) 
                  VALUES (:nombre, :cant, :p_id, :s_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':cant', $data['cant_etiquetas']);
        $stmt->bindParam(':p_id', $data['etiqueta_primaria_id']);
        $stmt->bindParam(':s_id', $data['etiqueta_secundaria_id']);
        return $stmt->execute();
    }
}
