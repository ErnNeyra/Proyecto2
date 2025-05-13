<?php
require_once 'openai_config.php';

function generarLogo($descripcion) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/generations');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ]);

    $payload = [
        'prompt' => 'Diseña un logotipo minimalista para un emprendimiento. Usa un estilo simple, moderno y limpio, tipo vectorial, con un fondo blanco. El logotipo debe incluir un símbolo representativo relacionado con el negocio. No incluyas texto. El diseño debe estar centrado, sin bordes ni elementos 3D. Ten en cuenta estas especificaciones del cliente: ' . $descripcion,
        'n' => 1,
        'size' => '1024x1024',
        'response_format' => 'url'
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
        
        if (isset($responseData['error'])) {
            return [
                'error' => 'Error de OpenAI: ' . $responseData['error']['message'],
                'debug' => $debugResponse,
                'detalles_error' => [
                    'código' => isset($responseData['error']['code']) ? $responseData['error']['code'] : 'No disponible',
                    'tipo' => isset($responseData['error']['type']) ? $responseData['error']['type'] : 'No disponible',
                    'param' => isset($responseData['error']['param']) ? $responseData['error']['param'] : 'No disponible',
                    'mensaje_completo' => isset($responseData['error']['message']) ? $responseData['error']['message'] : 'No disponible'
                ]
            ];
        }
        
        if (isset($responseData['data'][0]['url'])) {
            $imageUrl = $responseData['data'][0]['url'];
            
            // Descargar la imagen
            $imageContent = file_get_contents($imageUrl);
            if ($imageContent === false) {
                throw new Exception('No se pudo descargar la imagen');
            }
            
            // Generar nombre único para la imagen
            $fileName = 'logo_' . uniqid() . '.png';
            $filePath = __DIR__ . '/img/' . $fileName;
            
            // Guardar la imagen
            if (file_put_contents($filePath, $imageContent) === false) {
                throw new Exception('No se pudo guardar la imagen');
            }
            
            return [
                'success' => true,
                'imageUrl' => './util/img/' . $fileName
            ];
        } else {
            return ['error' => 'No se pudo generar el logo'];
        }
    } catch (Exception $e) {
        return ['error' => 'Error al generar el logo: ' . $e->getMessage()];
    } finally {
        curl_close($ch);
    }
}

// Procesar la solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['descripcion'])) {
        $resultado = generarLogo($data['descripcion']);
        header('Content-Type: application/json');
        echo json_encode($resultado);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'No se proporcionó una descripción']);
    }
}
?>