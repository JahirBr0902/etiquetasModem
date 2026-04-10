<?php

require_once __DIR__ . '/Model.php';

class Modem extends Model {
    
    public function create($lote_id, $modelo_id, $sn, $ssid, $password) {
        $query = "INSERT INTO modems (lote_id, modelo_id, sn, ssid, password, estado) 
                  VALUES (:lote_id, :modelo_id, :sn, :ssid, :password, 'PENDIENTE')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':lote_id', (int)$lote_id, PDO::PARAM_INT);
        $stmt->bindValue(':modelo_id', (int)$modelo_id, PDO::PARAM_INT);
        $stmt->bindValue(':sn', $sn, PDO::PARAM_STR);
        $stmt->bindValue(':ssid', $ssid, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function update($id, $modelo_id, $sn, $ssid, $password) {
        $query = "UPDATE modems SET modelo_id = :modelo_id, sn = :sn, ssid = :ssid, password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->bindValue(':modelo_id', (int)$modelo_id, PDO::PARAM_INT);
        $stmt->bindValue(':sn', $sn, PDO::PARAM_STR);
        $stmt->bindValue(':ssid', $ssid, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateEstado($id, $nuevoEstado) {
        $query = "UPDATE modems SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getByLote($lote_id) {
        $query = "SELECT m.*, mod.nombre as modelo_nombre 
                  FROM modems m
                  LEFT JOIN modelos_modem mod ON m.modelo_id = mod.id
                  WHERE m.lote_id = :lote_id 
                  ORDER BY m.id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':lote_id', (int)$lote_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $query = "DELETE FROM modems WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
