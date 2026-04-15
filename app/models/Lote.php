<?php

require_once __DIR__ . '/Model.php';

class Lote extends Model {
    
    public function getAll() {
        $query = "SELECT * FROM lotes ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $descripcion = '') {
        $query = "INSERT INTO lotes (nombre, descripcion, estado) VALUES (:nombre, :desc, 'NUEVO') RETURNING id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':desc', $descripcion);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    public function updateEstado($id, $nuevoEstado) {
        $fechaFinalizacion = ($nuevoEstado === 'COMPLETADO' || $nuevoEstado === 'IMPRESO') ? "fecha_finalizacion = CURRENT_TIMESTAMP" : "fecha_finalizacion = NULL";
        $query = "UPDATE lotes SET estado = :estado, $fechaFinalizacion WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $id);
        
        $res = $stmt->execute();

        // Si el lote se marca como IMPRESO, actualizamos el estado de impresión de sus ítems
        if ($res && $nuevoEstado === 'IMPRESO') {
            $queryItems = "UPDATE lote_modems SET estado_impresion = 'IMPRESO' WHERE lote_id = :l_id";
            $stmtItems = $this->conn->prepare($queryItems);
            $stmtItems->bindParam(':l_id', $id);
            $stmtItems->execute();
        }

        return $res;
    }
}
