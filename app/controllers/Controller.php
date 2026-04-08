<?php

class Controller {
    // Método para enviar respuestas JSON exitosas
    protected function success($data = [], $message = "Operación exitosa") {
        return [
            "status" => "success",
            "message" => $message,
            "data" => $data
        ];
    }

    // Método para enviar respuestas JSON de error
    protected function error($message = "Ocurrió un error", $code = 400) {
        http_response_code($code);
        return [
            "status" => "error",
            "message" => $message
        ];
    }
}
