<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Modem.php';

class ModemsController extends Controller {
    private $modemModel;

    public function __construct() {
        $this->modemModel = new Modem();
    }

    public function guardar($data) {
        if (empty($data['modelo_id']) || empty($data['sn'])) {
            return $this->error("Faltan datos obligatorios (Modelo o SN)");
        }

        try {
            if (isset($data['id']) && !empty($data['id'])) {
                $this->modemModel->update($data['id'], $data['modelo_id'], $data['sn'], $data['ssid'] ?? '', $data['password'] ?? '');
                return $this->success([], "Equipo actualizado correctamente");
            } else {
                $this->modemModel->create($data['lote_id'], $data['modelo_id'], $data['sn'], $data['ssid'] ?? '', $data['password'] ?? '');
                return $this->success([], "Modem guardado correctamente");
            }
        } catch (Exception $e) { 
            return $this->error("Error al procesar: " . $e->getMessage()); 
        }
    }

    public function cambiarEstado($data) {
        if (empty($data['id']) || empty($data['nuevo_estado'])) return $this->error("Faltan datos");
        try {
            $this->modemModel->updateEstado($data['id'], $data['nuevo_estado']);
            return $this->success([], "Estado actualizado");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function listarPorLote($data) {
        if (empty($data['lote_id'])) return $this->error("ID de lote no especificado");
        try {
            return $this->success($this->modemModel->getByLote($data['lote_id']));
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function eliminar($data) {
        if (empty($data['id'])) return $this->error("ID no especificado");
        try {
            $this->modemModel->delete($data['id']);
            return $this->success([], "Equipo eliminado");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }
}
