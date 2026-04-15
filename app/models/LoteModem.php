<?php

require_once __DIR__ . '/Model.php';

class LoteModem extends Model {
    
    public function assign($lote_id, $modem_id) {
        $query = "INSERT INTO lote_modems (lote_id, modem_id) VALUES (:lote_id, :modem_id)
                  ON CONFLICT (lote_id, modem_id) DO NOTHING";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':lote_id', (int)$lote_id, PDO::PARAM_INT);
        $stmt->bindValue(':modem_id', (int)$modem_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getByLote($lote_id) {
        $query = "SELECT lm.*, m.sn, m.ssid, m.password, m.modelo_id, m.estado_inventario, mod.nombre as modelo_nombre 
                  FROM lote_modems lm
                  JOIN modems m ON lm.modem_id = m.id
                  LEFT JOIN modelos_modem mod ON m.modelo_id = mod.id
                  WHERE lm.lote_id = :lote_id 
                  ORDER BY lm.id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':lote_id', (int)$lote_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateEstadoImpresion($lote_id, $modem_id, $nuevoEstado) {
        $query = "UPDATE lote_modems SET estado_impresion = :estado 
                  WHERE lote_id = :lote_id AND modem_id = :modem_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $stmt->bindValue(':lote_id', (int)$lote_id, PDO::PARAM_INT);
        $stmt->bindValue(':modem_id', (int)$modem_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function remove($lote_id, $modem_id) {
        $query = "DELETE FROM lote_modems WHERE lote_id = :lote_id AND modem_id = :modem_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':lote_id', (int)$lote_id, PDO::PARAM_INT);
        $stmt->bindValue(':modem_id', (int)$modem_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
