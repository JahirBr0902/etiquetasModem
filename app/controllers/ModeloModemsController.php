<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ModeloModem.php';

class ModeloModemsController extends Controller {
    private $modeloModel;

    public function __construct() {
        $this->modeloModel = new ModeloModem();
    }

    public function listar() {
        try {
            return $this->success($this->modeloModel->getAll());
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function guardar($data) {
        try {
            if (isset($data['id']) && !empty($data['id'])) {
                $this->modeloModel->update($data);
                return $this->success([], "Modelo actualizado correctamente");
            } else {
                $this->modeloModel->create($data);
                return $this->success([], "Modelo de modem guardado");
            }
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }
}
