<?php
require_once('openai_config.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['descripcion'])) {
        $resultado = mejorarDescripcion($data['descripcion']);
        echo json_encode($resultado);
    } else {
        echo json_encode(['error' => 'No se proporcionó descripción']);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
?>