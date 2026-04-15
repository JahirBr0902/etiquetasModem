<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/HistorialModem.php';

class HistorialController extends Controller {
    private $historialModel;

    public function __construct() {
        $this->historialModel = new HistorialModem();
    }

    public function listarPorModem($data) {
        if (empty($data['modem_id'])) return $this->error("ID de módem no especificado");
        try {
            return $this->success($this->historialModel->getByModem($data['modem_id']));
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }
}
