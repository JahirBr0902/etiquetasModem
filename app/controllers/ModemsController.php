<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Modem.php';
require_once __DIR__ . '/../models/LoteModem.php';
require_once __DIR__ . '/../models/HistorialModem.php';

class ModemsController extends Controller {
    private $modemModel;
    private $loteModemModel;
    private $historialModel;

    public function __construct() {
        $this->modemModel = new Modem();
        $this->loteModemModel = new LoteModem();
        $this->historialModel = new HistorialModem();
    }

    public function listar() {
        try {
            return $this->success($this->modemModel->getAll());
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function buscarPorSN($data) {
        if (empty($data['sn'])) return $this->error("SN no especificado");
        try {
            $modem = $this->modemModel->getBySN($data['sn']);
            if ($modem) return $this->success($modem);
            return $this->error("No encontrado", 404);
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function guardar($data) {
        if (empty($data['modelo_id']) || empty($data['sn'])) {
            return $this->error("Faltan datos obligatorios (Modelo o SN)");
        }

        try {
            $modem_id = null;
            $esNuevoModem = false;
            $msg = "";

            // 1. Verificamos si ya existe por SN (para flujo híbrido)
            $existente = $this->modemModel->getBySN($data['sn']);
            
            if ($existente) {
                $modem_id = $existente['id'];
                $this->modemModel->update(array_merge($data, ['id' => $modem_id, 'estado_inventario' => $data['estado_inventario'] ?? $existente['estado_inventario']]));
                $msg = "Equipo actualizado";
            } else {
                $modem_id = $this->modemModel->create($data);
                $esNuevoModem = true;
                $this->historialModel->registrar($modem_id, 'REGISTRO', null, "Registro inicial del equipo en sistema");
                $msg = "Equipo registrado en inventario";
            }

            // 2. Si viene un lote_id, lo asignamos
            if (!empty($data['lote_id'])) {
                $this->loteModemModel->assign($data['lote_id'], $modem_id);
                $this->historialModel->registrar($modem_id, 'ASIGNACION_LOTE', $data['lote_id'], "Asignado a la orden de producción");
                $msg = $esNuevoModem ? "Equipo registrado y asignado al lote" : "Equipo asignado al lote";
            }

            return $this->success(['id' => $modem_id], $msg);
        } catch (Exception $e) { 
            return $this->error("Error al procesar: " . $e->getMessage()); 
        }
    }

    public function actualizarEstado($data) {
        if (empty($data['id']) || empty($data['nuevo_estado'])) return $this->error("Faltan datos");
        try {
            $this->modemModel->updateEstadoInventario($data['id'], $data['nuevo_estado']);
            $this->historialModel->registrar($data['id'], $data['nuevo_estado'], null, $data['notas'] ?? "Cambio de estado manual");
            return $this->success([], "Estado del inventario actualizado");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function eliminar($data) {
        if (empty($data['id'])) return $this->error("ID no especificado");
        try {
            $this->modemModel->delete($data['id']);
            return $this->success([], "Equipo eliminado del sistema");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }
}
