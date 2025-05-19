<?php
header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require('../util/config.php');
require('../util/depurar.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit;
}

if (!isset($_SESSION['usuario']['usuario']) || !isset($_SESSION['usuario']['id_usuario'])) {
     echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para comentar.']);
     exit;
}

$idProducto = isset($_POST['id_producto']) ? depurar($_POST['id_producto']) : null;
$valoracion = isset($_POST['valoracion']) ? depurar($_POST['valoracion']) : null;
$comentario = isset($_POST['comentario']) ? depurar($_POST['comentario']) : null;

$usuarioAlias = $_SESSION['usuario']['usuario'];
// $idUsuario = $_SESSION['usuario']['id_usuario']; // No se usa directamente en este script, pero es bueno tenerlo si lo necesitas

if (!$idProducto || !is_numeric($idProducto) || !$valoracion || !is_numeric($valoracion) || $valoracion < 1 || $valoracion > 5 || $comentario === null) {
    echo json_encode(['success' => false, 'message' => 'Datos de comentario inválidos.']);
    exit;
}

$idProducto = (int)$idProducto;
$valoracion = (int)$valoracion;

// Usamos la tabla comentarios_producto
$sql = "INSERT INTO comentarios_producto (id_producto, usuario_alias, valoracion, comentario) VALUES (?, ?, ?, ?)";

$stmt = $_conexion->prepare($sql);

if ($stmt === false) {
    error_log("Error al preparar la consulta de inserción de comentario de producto: " . $_conexion->error);
    echo json_encode(['success' => false, 'message' => 'Error interno al guardar el comentario.']);
    exit;
}

// Vincular parámetros: i: integer (id_producto), s: string (usuario_alias), i: integer (valoracion), s: string (comentario)
$stmt->bind_param("isis", $idProducto, $usuarioAlias, $valoracion, $comentario);

if ($stmt->execute()) {
    $nuevoComentarioId = $stmt->insert_id;

    // Recuperar los datos completos del comentario recién insertado para devolverlos al JS
    $sqlNuevo = "SELECT cp.*, u.usuario AS nombre_usuario_comentario, u.foto_perfil
                 FROM comentarios_producto cp
                 JOIN usuario u ON cp.usuario_alias = u.usuario
                 WHERE cp.id_comentario = ?";
    $stmtNuevo = $_conexion->prepare($sqlNuevo);
    $stmtNuevo->bind_param("i", $nuevoComentarioId);
    $stmtNuevo->execute();
    $nuevoComentarioData = $stmtNuevo->get_result()->fetch_assoc();
    $stmtNuevo->close();

    echo json_encode([
        'success' => true,
        'message' => 'Comentario guardado con éxito.',
        'comentario' => $nuevoComentarioData
    ]);

} else {
    error_log("Error al ejecutar la consulta de inserción de comentario de producto: " . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario en la base de datos.']);
}

$stmt->close();
$_conexion->close();
?>