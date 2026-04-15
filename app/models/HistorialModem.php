<?php

require_once __DIR__ . '/Model.php';

class HistorialModem extends Model {
    
    public function registrar($modem_id, $tipo, $lote_id = null, $notas = null) {
        $query = "INSERT INTO historial_modems (modem_id, tipo_movimiento, lote_id, notas) 
                  VALUES (:modem_id, :tipo, :lote_id, :notas)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':modem_id', (int)$modem_id, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':lote_id', $lote_id ? (int)$lote_id : null, $lote_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':notas', $notas, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getByModem($modem_id) {
        $query = "SELECT h.*, l.nombre as lote_nombre 
                  FROM historial_modems h
                  LEFT JOIN lotes l ON h.lote_id = l.id
                  WHERE h.modem_id = :modem_id 
                  ORDER BY h.fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':modem_id', (int)$modem_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
