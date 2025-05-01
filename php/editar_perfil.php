<?php
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.html");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // **CONFIGURACIÓN DE LA BASE DE DATOS** (Asegúrate de que coincida con tu configuración)
    $servername = "localhost";
    $username = "usuario_db";
    $password = "contraseña_db";
    $dbname = "nombre_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    $error = "";
    $success = "";
    $nombre_actual = "";
    $email_actual = "";

    // Obtener la información actual del usuario para pre-llenar el formulario
    $sql_select = "SELECT nombre, email FROM usuarios WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $user_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();

    if ($result_select->num_rows == 1) {
        $user_data = $result_select->fetch_assoc();
        $nombre_actual = htmlspecialchars($user_data['nombre']);
        $email_actual = htmlspecialchars($user_data['email']);
    } else {
        $error = "Error al cargar la información del perfil.";
    }
    $stmt_select->close();

    // Procesar el formulario cuando se envíe
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre_nuevo = $_POST['nombre'] ?? '';
        $email_nuevo = $_POST['email'] ?? '';
        $password_actual = $_POST['password_actual'] ?? '';
        $password_nuevo = $_POST['password_nuevo'] ?? '';
        $confirmar_password_nuevo = $_POST['confirmar_password_nuevo'] ?? '';

        // **VALIDACIÓN DE DATOS** (¡Esta es una validación básica, debes añadir más!)
        if (empty($nombre_nuevo)) {
            $error = "El nombre es obligatorio.";
        } elseif (empty($email_nuevo) || !filter_var($email_nuevo, FILTER_VALIDATE_EMAIL)) {
            $error = "El email no es válido.";
        } else {
            // Actualizar la información básica (nombre y email)
            $sql_update = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssi", $nombre_nuevo, $email_nuevo, $user_id);
            if ($stmt_update->execute()) {
                $success = "Perfil actualizado correctamente.";
                // Actualizar las variables para mostrar la nueva información en el formulario
                $nombre_actual = htmlspecialchars($nombre_nuevo);
                $email_actual = htmlspecialchars($email_nuevo);

                // **GESTIÓN DE LA CONTRASEÑA (OPCIONAL)**
                if (!empty($password_nuevo)) {
                    // Verificar la contraseña actual (¡Esto requiere que tengas la contraseña hasheada en la base de datos!)
                    $sql_check_password = "SELECT password FROM usuarios WHERE id = ?";
                    $stmt_check_password = $conn->prepare($sql_check_password);
                    $stmt_check_password->bind_param("i", $user_id);
                    $stmt_check_password->execute();
                    $result_check_password = $stmt_check_password->get_result();
                    $row_password = $result_check_password->fetch_assoc();
                    $hashed_password_actual = $row_password['password'];
                    $stmt_check_password->close();

                    if (password_verify($password_actual, $hashed_password_actual)) {
                        if ($password_nuevo === $confirmar_password_nuevo) {
                            $hashed_password_nuevo = password_hash($password_nuevo, PASSWORD_DEFAULT);
                            $sql_update_password = "UPDATE usuarios SET password = ? WHERE id = ?";
                            $stmt_update_password = $conn->prepare($sql_update_password);
                            $stmt_update_password->bind_param("si", $hashed_password_nuevo, $user_id);
                            if ($stmt_update_password->execute()) {
                                $success .= " Contraseña actualizada.";
                            } else {
                                $error .= " Error al actualizar la contraseña.";
                            }
                            $stmt_update_password->close();
                        } else {
                            $error .= " La nueva contraseña y la confirmación no coinciden.";
                        }
                    } else {
                        $error .= " La contraseña actual es incorrecta.";
                    }
                }
            } else {
                $error = "Error al actualizar el perfil.";
            }
            $stmt_update->close();
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style2.css">
    <style>
        /* Estilos adicionales para el formulario de editar perfil */
        .edit-profile-panel {
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
        }

        .edit-profile-panel h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #374151;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: medium;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-sizing: border-box;
        }

        .form-group .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-actions button {
            background-color: #facc15; /* Tailwind yellow-500 */
            color: black;
            font-weight: bold;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            width: 100%;
        }

        .form-actions button:hover {
            background-color: #eab308; /* Tailwind yellow-600 */
        }

        .success-message {
            color: #16a34a; /* Tailwind green-500 */
            margin-bottom: 1rem;
            font-weight: medium;
        }

        .error-message-overall {
            color: #ef4444; /* Tailwind red-500 */
            margin-bottom: 1rem;
            font-weight: medium;
        }
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
                <a href="/panel_usuario.php" class="text-gray-700 hover:text-black mr-4">Mi Panel</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-8">
        <div class="edit-profile-panel">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Editar Perfil</h2>

            <?php if ($success): ?>
                <p class="success-message"><?php echo $success; ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="error-message-overall"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $nombre_actual; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $email_actual; ?>">
                </div>

                <div class="form-group">
                    <label for="password_actual">Contraseña Actual (para cambiar la contraseña):</label>
                    <input type="password" id="password_actual" name="password_actual">
                </div>

                <div class="form-group">
                    <label for="password_nuevo">Nueva Contraseña:</label>
                    <input type="password" id="password_nuevo" name="password_nuevo">
                </div>

                <div class="form-group">
                    <label for="confirmar_password_nuevo">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="confirmar_password_nuevo" name="confirmar_password_nuevo">
                </div>

                <div class="form-actions">
                    <button type="submit">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>