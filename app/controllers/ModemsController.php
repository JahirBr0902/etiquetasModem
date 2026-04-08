<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Modem.php';

class ModemsController extends Controller {
    private $modemModel;

    public function __construct() {
        $this->modemModel = new Modem();
    }

    public function guardar($data) {
        if (empty($data['lote_id']) || empty($data['modelo_id']) || empty($data['sn'])) {
            return $this->error("Faltan datos obligatorios (Lote, Modelo o SN)");
        }

        try {
            $this->modemModel->create(
                $data['lote_id'], 
                $data['modelo_id'], 
                $data['sn'], 
                $data['password'] ?? ''
            );
            return $this->success([], "Modem guardado correctamente");
        } catch (Exception $e) { 
            return $this->error("Error al guardar en la base de datos: " . $e->getMessage()); 
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
}
