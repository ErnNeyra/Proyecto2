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
$modo = isset($input['modo']) ? $input['modo'] : 'auto'; // Puedes enviar 'auto', 'array' o 'multipart'

if (!$descripcion) {
    echo json_encode(['error' => 'Descripción requerida']);
    exit;
}

$url = 'https://api.ideogram.ai/v1/ideogram-v3/generate';

$postFields = [
    'prompt' => $descripcion,
    'aspect_ratio' => '1x1',
    'rendering_speed' => 'TURBO'
];

$headers = [
    'Api-Key: ' . $apiKey
];

$response = false;
$error = null;

// Si modo es 'multipart' o 'auto', intenta multipart manual
if ($modo === 'multipart' || $modo === 'auto') {
    $boundary = uniqid();
    $delimiter = '-------------' . $boundary;
    $postData = '';
    foreach ($postFields as $name => $content) {
        $postData .= "--$delimiter\r\n";
        $postData .= "Content-Disposition: form-data; name=\"$name\"\r\n\r\n";
        $postData .= $content . "\r\n";
    }
    $postData .= "--$delimiter--\r\n";

    $headersMultipart = $headers;
    $headersMultipart[] = "Content-Type: multipart/form-data; boundary=$delimiter";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headersMultipart);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if (isset($result['images'][0]['url'])) {
        echo json_encode(['success' => true, 'imageUrl' => $result['images'][0]['url']]);
        exit;
    }
    // Si modo es 'multipart', no sigas probando
    if ($modo === 'multipart') {
        $apiError = isset($result['error']) ? $result['error'] : 'No se pudo generar la imagen';
        echo json_encode(['error' => $apiError, 'api_response' => $result]);
        exit;
    }
}

// Si modo es 'array' o 'auto', intenta método array PHP (tu método original)
if ($modo === 'array' || $modo === 'auto') {
    $headersArray = $headers;
    $headersArray[] = 'Content-Type: multipart/form-data';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    if (isset($result['data'][0]['url'])) {
        echo json_encode(['success' => true, 'imageUrl' => $result['data'][0]['url']]);
        exit;
    } else {
        $apiError = isset($result['error']) ? $result['error'] : 'No se pudo generar la imagen';
        echo json_encode(['error' => $apiError, 'api_response' => $result]);
        exit;
    }
}

echo json_encode(['error' => 'No se pudo generar la imagen', 'curl_error' => $error]);