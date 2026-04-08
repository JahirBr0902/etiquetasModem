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
        $query = "UPDATE lotes SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevoEstado);
        $stmt->bindParam(':id', $id);
        
        // Si el lote se marca como IMPRESO o COMPLETADO, actualizamos todos sus modems
        if ($nuevoEstado === 'IMPRESO' || $nuevoEstado === 'COMPLETADO') {
            $modemEstado = ($nuevoEstado === 'IMPRESO') ? 'IMPRESO' : 'COMPLETADO';
            $queryModems = "UPDATE modems SET estado = :m_estado WHERE lote_id = :l_id";
            $stmtModems = $this->conn->prepare($queryModems);
            $stmtModems->bindParam(':m_estado', $modemEstado);
            $stmtModems->bindParam(':l_id', $id);
            $stmtModems->execute();
        }

        return $stmt->execute();
    }
}
