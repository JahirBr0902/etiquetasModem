<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/LoteModem.php';
require_once __DIR__ . '/../models/HistorialModem.php';

class LoteModemsController extends Controller {
    private $loteModemModel;
    private $historialModel;

    public function __construct() {
        $this->loteModemModel = new LoteModem();
        $this->historialModel = new HistorialModem();
    }

    public function listarPorLote($data) {
        if (empty($data['lote_id'])) return $this->error("ID de lote no especificado");
        try {
            return $this->success($this->loteModemModel->getByLote($data['lote_id']));
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function desvincular($data) {
        if (empty($data['lote_id']) || empty($data['modem_id'])) return $this->error("Faltan datos");
        try {
            $this->loteModemModel->remove($data['lote_id'], $data['modem_id']);
            $this->historialModel->registrar($data['modem_id'], 'DESVINCULACION_LOTE', $data['lote_id'], "Eliminado de la orden de producción");
            return $this->success([], "Equipo quitado del lote");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function asignarMasivo($data) {
        if (empty($data['lote_id']) || empty($data['modem_ids']) || !is_array($data['modem_ids'])) {
            return $this->error("Faltan datos o formato incorrecto");
        }
        try {
            $count = 0;
            foreach ($data['modem_ids'] as $modem_id) {
                if ($this->loteModemModel->assign($data['lote_id'], $modem_id)) {
                    $this->historialModel->registrar($modem_id, 'ASIGNACION_LOTE', $data['lote_id'], "Asignación masiva a orden de producción");
                    $count++;
                }
            }
            return $this->success([], "Se asignaron $count equipos correctamente");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function actualizarEstadoImpresion($data) {
        if (empty($data['lote_id']) || empty($data['modem_id']) || empty($data['nuevo_estado'])) {
            return $this->error("Faltan datos");
        }
        try {
            $this->loteModemModel->updateEstadoImpresion($data['lote_id'], $data['modem_id'], $data['nuevo_estado']);
            if ($data['nuevo_estado'] === 'IMPRESO') {
                $this->historialModel->registrar($data['modem_id'], 'IMPRESION', $data['lote_id'], "Etiqueta impresa correctamente");
            }
            return $this->success([], "Estado de impresión actualizado");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }
}
