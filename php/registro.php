<?php
// Establecer el tipo de contenido a JSON para que JavaScript pueda entender la respuesta
header('Content-Type: application/json');

// 1. Recibir y Sanear Datos del Formulario
$nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING));
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$password = $_POST['password'] ?? '';
$confirmar_password = $_POST['confirmar_password'] ?? '';

$errores = [];

// 2. Validación del Servidor
if (empty($nombre)) {
    $errores['nombre'] = 'El nombre completo es obligatorio.';
}
if (empty($email)) {
    $errores['email'] = 'El correo electrónico es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores['email'] = 'El formato del correo electrónico no es válido.';
}
if (empty($password)) {
    $errores['password'] = 'La contraseña es obligatoria.';
} elseif (strlen($password) < 6) {
    $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
}
if ($password !== $confirmar_password) {
    $errores['confirmar_password'] = 'Las contraseñas no coinciden.';
}

// Si hay errores de validación, los enviamos de vuelta al frontend
if (!empty($errores)) {
    echo json_encode(['success' => false, 'errors' => $errores]);
    exit();
}

// 3. Conexión a la Base de Datos
$servername = "localhost"; // Reemplaza con tu servidor
$username = "tu_usuario_db"; // Reemplaza con tu usuario de la base de datos
$dbpassword = "tu_contraseña_db"; // Reemplaza con tu contraseña de la base de datos
$dbname = "tu_nombre_db"; // Reemplaza con el nombre de tu base de datos

$conn = new mysqli($servername, $username, $dbpassword, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos: ' . $conn->connect_error]);
    exit();
}

// 4. Verificar Existencia de Email (Podrías también verificar el nombre de usuario si lo tienes)
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?"); // Asumo que tienes una columna 'email'
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'errors' => ['email' => 'Este correo electrónico ya está registrado.']]);
    $stmt->close();
    $conn->close();
    exit();
}

// 5. Hashear la Contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// 6. Insertar Datos del Nuevo Usuario
$stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)"); // Asumo que tienes columnas 'nombre', 'email' y 'password'
$stmt->bind_param("sss", $nombre, $email, $password_hash);

if ($stmt->execute()) {
    // Registro exitoso
    echo json_encode(['success' => true, 'message' => 'Registro completado con éxito. ¡Bienvenido a We-Connect!']);
} else {
    // Error al insertar
    echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $stmt->error]);
}

// Cerrar la sentencia y la conexión
$stmt->close();
$conn->close();
?>