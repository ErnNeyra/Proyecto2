<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.html");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Conectar a la base de datos (reemplaza con tus credenciales)
    $conn = new mysqli("localhost", "usuario_db", "contraseña_db", "nombre_db");
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    $sql = "SELECT nombre, email FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user_data = $result->fetch_assoc();
        $nombre_usuario = $user_data['nombre'];
        $email_usuario = $user_data['email'];
    } else {
        // Manejar el caso en que no se encuentre el usuario (posible error)
        $nombre_usuario = "Error al cargar el nombre";
        $email_usuario = "Error al cargar el email";
    }

    $stmt->close();
    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Panel de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style2.css">
    <style>
        /* ... tus estilos CSS ... */
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="index2.html" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="/listado.html" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="/contacto.html" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <a href="/registro.html" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="/login.html" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
                <a href="/panel_usuario.php" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition duration-200">Mi Panel</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-8">
        <div class="user-panel">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Panel de Usuario</h2>
            <div class="mb-4 account-info">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Información de la Cuenta</h3>
                <p class="text-gray-600">Nombre: <span id="user-name"><?php echo htmlspecialchars($nombre_usuario); ?></span></p>
                <p class="text-gray-600">Email: <span id="user-email"><?php echo htmlspecialchars($email_usuario); ?></span></p>
            </div>
            <div class="mb-4 actions">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Acciones</h3>
                <ul class="space-y-2">
                    <li><a href="/editar_perfil.php">Editar Perfil</a></li>
                    <li>
                        <form action="/logout.php" method="post">
                            <button type="submit" class="logout-button">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </main>

    </body>
</html>