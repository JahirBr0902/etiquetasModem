<?php

require_once __DIR__ . '/Model.php';

class Modem extends Model {
    
    public function create($data) {
        $query = "INSERT INTO modems (modelo_id, sn, ssid, password, estado_inventario) 
                  VALUES (:modelo_id, :sn, :ssid, :password, :estado)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':modelo_id', (int)$data['modelo_id'], PDO::PARAM_INT);
        $stmt->bindValue(':sn', $data['sn'], PDO::PARAM_STR);
        $stmt->bindValue(':ssid', $data['ssid'], PDO::PARAM_STR);
        $stmt->bindValue(':password', $data['password'], PDO::PARAM_STR);
        $stmt->bindValue(':estado', $data['estado_inventario'] ?? 'BODEGA', PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($data) {
        $query = "UPDATE modems SET 
                    modelo_id = :modelo_id, 
                    sn = :sn, 
                    ssid = :ssid, 
                    password = :password,
                    estado_inventario = :estado
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int)$data['id'], PDO::PARAM_INT);
        $stmt->bindValue(':modelo_id', (int)$data['modelo_id'], PDO::PARAM_INT);
        $stmt->bindValue(':sn', $data['sn'], PDO::PARAM_STR);
        $stmt->bindValue(':ssid', $data['ssid'], PDO::PARAM_STR);
        $stmt->bindValue(':password', $data['password'], PDO::PARAM_STR);
        $stmt->bindValue(':estado', $data['estado_inventario'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getBySN($sn) {
        $query = "SELECT m.*, mod.nombre as modelo_nombre 
                  FROM modems m
                  LEFT JOIN modelos_modem mod ON m.modelo_id = mod.id
                  WHERE m.sn = :sn";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':sn', $sn, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT m.*, mod.nombre as modelo_nombre 
                  FROM modems m
                  LEFT JOIN modelos_modem mod ON m.modelo_id = mod.id
                  ORDER BY m.fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateEstadoInventario($id, $nuevoEstado) {
        $query = "UPDATE modems SET estado_inventario = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM modems WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
