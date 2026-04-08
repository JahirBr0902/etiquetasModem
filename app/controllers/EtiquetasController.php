<?php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Etiqueta.php';

class EtiquetasController extends Controller {
    private $etiquetaModel;

    public function __construct() {
        $this->etiquetaModel = new Etiqueta();
    }

    public function listar() {
        try {
            $etiquetas = $this->etiquetaModel->getAll();
            return $this->success($etiquetas, "Etiquetas obtenidas correctamente");
        } catch (Exception $e) {
            return $this->error("Error al obtener etiquetas: " . $e->getMessage());
        }
    }

    public function guardar($data) {
        if (!isset($data['nombre']) || empty($data['nombre'])) {
            return $this->error("El nombre es obligatorio");
        }

        try {
            $this->etiquetaModel->create($data);
            return $this->success([], "Etiqueta guardada correctamente");
        } catch (Exception $e) {
            return $this->error("Error al guardar etiqueta: " . $e->getMessage());
        }
    }
}
