<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/EtiquetaTemplate.php';

class EtiquetaTemplatesController extends Controller {
    private $templateModel;

    public function __construct() {
        $this->templateModel = new EtiquetaTemplate();
    }

    public function listar() {
        try {
            return $this->success($this->templateModel->getAll());
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function guardar($data) {
        try {
            if (isset($data['id']) && !empty($data['id'])) {
                $this->templateModel->update($data);
                return $this->success([], "Diseño actualizado correctamente");
            } else {
                $this->templateModel->create($data);
                return $this->success([], "Template creado correctamente");
            }
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }

    public function obtener($data) {
        if (empty($data['id'])) return $this->error("ID no especificado");
        try {
            return $this->success($this->templateModel->getById($data['id']));
        } catch (Exception $e) { return $this->error($e->getMessage()); }
    }
}
