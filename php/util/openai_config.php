<?php
// Función para cargar variables de entorno
function loadEnv() {


    $envFile = __DIR__ . '/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $_ENV[$key] = $value;
            }
        }
    }
}

// Cargar variables de entorno
loadEnv();

// Verificar si la clave API existe
if (!isset($_ENV['OPENAI_API_KEY'])) {
    die('Error: OPENAI_API_KEY no está configurada en el archivo .env');
}

// Configuración de OpenAI
define('OPENAI_API_KEY', $_ENV['OPENAI_API_KEY']);

function mejorarDescripcion($descripcion) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ]);

    $payload = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Eres un asistente experto en marketing que mejora descripciones de negocios para hacerlas más atractivas y profesionales.'
            ],
            [
                'role' => 'user',
                'content' => 'Mejora esta descripción de negocio manteniendo la misma información pero haciéndola más atractiva y profesional. No coloques nada extra al texto mejorado y que la respuesta nunca se vea como una respuesta de ia, sino el texto mejorado directamente: ' . $descripcion
            ]
        ],
        'max_tokens' => 500,
        'temperature' => 0.7
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    try {
        $response = curl_exec($ch);
        
        if ($response === false) {
            throw new Exception(curl_error($ch));
        }
        
        $responseData = json_decode($response, true);
        
        // Guardar la respuesta completa para debugging
        $debugResponse = [
            'respuesta_completa' => $responseData,
            'respuesta_raw' => $response
        ];
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Error al decodificar la respuesta JSON: ' . json_last_error_msg(),
                'debug' => $debugResponse
            ];
        }
        
        if (isset($responseData['error'])) {
            return [
                'error' => 'Error de OpenAI: ' . $responseData['error']['message'],
                'debug' => $debugResponse
            ];
        }
        
        if (isset($responseData['choices'][0]['message']['content'])) {
            return [
                'success' => $responseData['choices'][0]['message']['content'],
                'debug' => $debugResponse
            ];
        } else {
            return [
                'error' => 'No se pudo procesar la respuesta de OpenAI: Estructura de respuesta inesperada',
                'debug' => $debugResponse
            ];
        }
    } catch (Exception $e) {
        return [
            'error' => 'Error al comunicarse con OpenAI: ' . $e->getMessage(),
            'debug' => isset($debugResponse) ? $debugResponse : ['error' => 'No se pudo obtener respuesta']
        ];
    } finally {
        curl_close($ch);
    }
}
?>