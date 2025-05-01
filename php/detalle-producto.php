<?php
    session_start();

    // Verificar si se ha proporcionado un ID de producto en la URL
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        // Si no hay ID o no es numérico, redirigir al listado
        header("Location: /listado.php");
        exit();
    }

    $producto_id = $_GET['id'];

    // **CONFIGURACIÓN DE LA BASE DE DATOS** (Asegúrate de que coincida con tu configuración)
    $servername = "localhost";
    $username = "usuario_db";
    $password = "contraseña_db";
    $dbname = "nombre_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    // Consulta para obtener los detalles del producto específico
    $sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen_url, u.nombre AS nombre_usuario
            FROM productos p
            INNER JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $producto = null;
    if ($result->num_rows == 1) {
        $producto = $result->fetch_assoc();
    } else {
        // Si no se encuentra el producto, podrías mostrar un mensaje de error o redirigir al listado
        $error_message = "Producto no encontrado.";
    }

    $stmt->close();
    $conn->close();
?>