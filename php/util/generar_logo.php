<?php
header('Content-Type: application/json');

// Cargar variables de entorno desde .env
function get_env_var($key) {
    $lines = file(__DIR__ . '/.env');
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        list($env_key, $env_val) = array_map('trim', explode('=', $line, 2));
        if ($env_key === $key) {
            return $env_val;
        }
    }
    return null;
}

$apiKey = trim(get_env_var('IDEOGRAM_API_KEY'));
if (!$apiKey) {
    echo json_encode(['error' => 'API key no encontrada']);
    exit;
}

// Obtener la descripción enviada por POST
$input = json_decode(file_get_contents('php://input'), true);
$descripcion = isset($input['descripcion']) ? trim($input['descripcion']) : '';

if (!$descripcion) {
    echo json_encode(['error' => 'Descripción requerida']);
    exit;
}

// Llamada a la API de Ideogram usando CURL con multipart/form-data
$url = 'https://api.ideogram.ai/v1/ideogram-v3/generate';

$postFields = [
    'prompt' => $descripcion,
    'aspect_ratio' => '1x1',
    'rendering_speed' => 'TURBO'
];

$headers = [
    'Api-Key: ' . $apiKey,
    'Content-Type: multipart/form-data'
];

// Log temporal para depuración
file_put_contents(__DIR__ . '/debug_headers.log', print_r($headers, true));

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);



if ($response === false) {
    echo json_encode(['error' => 'No se pudo conectar a la API de Ideogram', 'curl_error' => $error]);
    exit;
}

$result = json_decode($response, true);


if (isset($result['data'][0]['url'])) {
    echo json_encode(['success' => true, 'imageUrl' => $result['data'][0]['url']]);
} else {
    $apiError = isset($result['error']) ? $result['error'] : 'No se pudo generar la imagen';
    echo json_encode(['error' => $apiError, 'api_response' => $result]);
}