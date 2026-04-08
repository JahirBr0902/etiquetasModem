<?php
// Desactivar la visualización de errores HTML para no romper el JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Función para capturar errores y devolverlos como JSON
function handleFatalError() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR)) {
        echo json_encode([
            "status" => "error",
            "message" => "Error crítico de PHP: " . $error['message'],
            "file" => $error['file'],
            "line" => $error['line']
        ]);
    }
}
register_shutdown_function('handleFatalError');

try {
    require_once __DIR__ . '/../app/config/db.php';

    $controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : null;
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    if (!$controllerName || !$action) {
        throw new Exception("Controlador o acción no especificados");
    }

    $controllerFile = __DIR__ . "/../app/controllers/$controllerName.php";

    if (!file_exists($controllerFile)) {
        throw new Exception("El archivo del controlador '$controllerName' no existe");
    }

    require_once $controllerFile;
    
    if (!class_exists($controllerName)) {
        throw new Exception("La clase '$controllerName' no se encontró");
    }

    $controllerObject = new $controllerName();
    
    if (!method_exists($controllerObject, $action)) {
        throw new Exception("La acción '$action' no existe en el controlador");
    }

    $json = json_decode(file_get_contents("php://input"), true) ?? [];
    $data = array_merge($_GET, $_POST, $json);
    
    $response = $controllerObject->$action($data);
    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
