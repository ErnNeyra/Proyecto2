<?php
    session_start();
    require('../util/config.php'); // Asegúrate de que este archivo contiene tu conexión a la base de datos ($_conexion)

    // Inicializar variables para mensajes de error
    $error_message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener datos del formulario
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Realizar validaciones básicas (puedes añadir más)
        if (empty($username) || empty($password)) {
            $error_message = "Por favor, introduce tu nombre de usuario y contraseña.";
        } else {
            // Consulta a la base de datos para verificar las credenciales, incluyendo la foto de perfil
            $sql = "SELECT id_usuario, usuario, nombre, email, contrasena, foto_perfil FROM usuario WHERE usuario = ?";
            $stmt = $_conexion->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows == 1) {
                $row = $resultado->fetch_assoc();
                // Verificar la contraseña (usando password_verify si has hasheado las contraseñas)
                if (password_verify($password, $row['contrasena'])) {
                    // La autenticación fue exitosa, guardar la información del usuario en la sesión, incluyendo la foto de perfil
                    $_SESSION['usuario'] = array(
                        'id_usuario' => $row['id_usuario'],
                        'usuario' => $row['usuario'],
                        'nombre' => $row['nombre'],
                        'email' => $row['email'],
                        'foto_perfil' => $row['foto_perfil'] // Guardar la ruta o nombre del archivo de la foto de perfil
                        // Puedes guardar más información si es necesario
                    );

                    // Redirigir al usuario a la página de perfil o a la página principal
                    header("location: ../../index.php");
                    exit;
                } else {
                    $error_message = "Contraseña incorrecta.";
                }
            } else {
                $error_message = "Nombre de usuario no encontrado.";
            }

            $stmt->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="block text-gray-700 text-xl font-bold mb-4 text-center">Iniciar Sesión</h2>
        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Nombre de Usuario
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Nombre de Usuario" name="username">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Contraseña
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="Contraseña" name="password">
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Iniciar Sesión
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="#">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        </form>
        <p class="text-center text-gray-500 text-xs mt-4">
            &copy; 2025 We-Connect. Todos los derechos reservados.
        </p>
    </div>
</body>
</html>