<?php
// Este script procesa la solicitud AJAX para guardar un comentario y valoración de servicio.

header('Content-Type: application/json'); // Indicar que la respuesta es JSON

// Iniciar sesión para verificar si el usuario está logueado y obtener su ID/alias
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos de configuración y depuración
require('../util/config.php');
require('../util/depurar.php'); // Asumo que depurar.php está en ../util/

// Verificar que la solicitud sea POST y que el usuario esté logueado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit;
}

if (!isset($_SESSION['usuario']['usuario']) || !isset($_SESSION['usuario']['id_usuario'])) {
     echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para comentar.']);
     exit;
}

// Obtener y limpiar los datos recibidos por POST
$idServicio = isset($_POST['id_servicio']) ? depurar($_POST['id_servicio']) : null;
$valoracion = isset($_POST['valoracion']) ? depurar($_POST['valoracion']) : null;
$comentario = isset($_POST['comentario']) ? depurar($_POST['comentario']) : null;

// Obtener el alias y ID del usuario logueado desde la sesión
$usuarioAlias = $_SESSION['usuario']['usuario'];
$idUsuario = $_SESSION['usuario']['id_usuario']; // Asumo que el id_usuario también está en la sesión

// Validar datos básicos
if (!$idServicio || !is_numeric($idServicio) || !$valoracion || !is_numeric($valoracion) || $valoracion < 1 || $valoracion > 5 || $comentario === null) {
    echo json_encode(['success' => false, 'message' => 'Datos de comentario inválidos.']);
    exit;
}

// Convertir a tipos de datos adecuados
$idServicio = (int)$idServicio;
$valoracion = (int)$valoracion;

// Preparar la consulta SQL para insertar el comentario
// Usamos prepared statements para seguridad contra inyección SQL
$sql = "INSERT INTO comentarios_servicio (id_servicio, usuario_alias, valoracion, comentario) VALUES (?, ?, ?, ?)";

$stmt = $_conexion->prepare($sql);

if ($stmt === false) {
    // Log del error internamente en un sistema de producción
    error_log("Error al preparar la consulta de inserción de comentario: " . $_conexion->error);
    echo json_encode(['success' => false, 'message' => 'Error interno al guardar el comentario.']);
    exit;
}

// Vincular parámetros
// 'isi' -> i: integer (id_servicio), s: string (usuario_alias), i: integer (valoracion), s: string (comentario)
$stmt->bind_param("isis", $idServicio, $usuarioAlias, $valoracion, $comentario);

// Ejecutar la consulta
if ($stmt->execute()) {
    // Inserción exitosa
    // Puedes obtener el ID del comentario recién insertado si lo necesitas
    $nuevoComentarioId = $stmt->insert_id;

    // Opcional: Obtener los datos completos del comentario recién insertado para devolverlos al JS
    // Esto permite al JS mostrar el comentario tal como se guardó en la BD (incluyendo la fecha)
    $sqlNuevo = "SELECT cs.*, u.usuario AS nombre_usuario_comentario, u.foto_perfil
                 FROM comentarios_servicio cs
                 JOIN usuario u ON cs.usuario_alias = u.usuario
                 WHERE cs.id_comentario = ?";
    $stmtNuevo = $_conexion->prepare($sqlNuevo);
    $stmtNuevo->bind_param("i", $nuevoComentarioId);
    $stmtNuevo->execute();
    $nuevoComentarioData = $stmtNuevo->get_result()->fetch_assoc();
    $stmtNuevo->close();


    echo json_encode([
        'success' => true,
        'message' => 'Comentario guardado con éxito.',
        'comentario' => $nuevoComentarioData // Devolver los datos del nuevo comentario
    ]);

} else {
    // Error en la ejecución de la consulta
     error_log("Error al ejecutar la consulta de inserción de comentario: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario en la base de datos.']);
}

// Cerrar la conexión a la base de datos
$stmt->close();
$_conexion->close();
?>