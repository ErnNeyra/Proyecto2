<?php
session_start(); // Iniciar la sesión para mantener al usuario logueado

header('Content-Type: application/json');

// Recibir y Sanear Datos del Formulario
$usuario_email = trim(filter_input(INPUT_POST, 'usuario_email', FILTER_SANITIZE_STRING));
$password = $_POST['password'] ?? '';

// Validación básica del servidor
if (empty($usuario_email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, introduce tu nombre de usuario/correo electrónico y contraseña.']);
    exit();
}

// Conexión a la Base de Datos
$servername = "localhost"; // Reemplaza con tu servidor
$username = "tu_usuario_db"; // Reemplaza con tu usuario de la base de datos
$dbpassword = "tu_contraseña_db"; // Reemplaza con tu contraseña de la base de datos
$dbname = "tu_nombre_db"; // Reemplaza con el nombre de tu base de datos

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']);
    exit();
}

// Buscar al usuario por nombre de usuario o correo electrónico
$stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE usuario = ? OR email = ?"); // Ajusta las columnas según tu tabla
$stmt->bind_param("ss", $usuario_email, $usuario_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
    $hashed_password = $row['password'];

    // Verificar la contraseña
    if (password_verify($password, $hashed_password)) {
        // Inicio de sesión exitoso
        $_SESSION['user_id'] = $user_id; // Guardar el ID del usuario en la sesión
        echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso.']);
    } else {
        // Contraseña incorrecta
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
    }
} else {
    // No se encontró al usuario
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
}

$stmt->close();
$conn->close();
?>