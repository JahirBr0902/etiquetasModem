<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Lote.php';

class LotesController extends Controller {
    private $loteModel;

    public function __construct() {
        $this->loteModel = new Lote();
    }

    public function listar() {
        try {
            return $this->success($this->loteModel->getAll());
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function guardar($data) {
        if (empty($data['nombre'])) return $this->error("Nombre obligatorio");
        try {
            $id = $this->loteModel->create($data['nombre'], $data['descripcion'] ?? '');
            return $this->success(['id' => $id]);
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function cambiarEstado($data) {
        if (empty($data['lote_id']) || empty($data['nuevo_estado'])) return $this->error("Faltan datos");
        try {
            $this->loteModel->updateEstado($data['lote_id'], $data['nuevo_estado']);
            return $this->success([], "Estado del lote actualizado");
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }
}
