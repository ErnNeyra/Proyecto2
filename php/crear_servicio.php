<?php
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.html"); // O una página de error de acceso no autorizado
        exit();
    }

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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? '';
        $user_id = $_SESSION['user_id'];

        // **VALIDACIÓN DE DATOS**
        if (empty($nombre)) {
            $error = "El nombre del servicio es obligatorio.";
        } elseif (empty($descripcion)) {
            $error = "La descripción del servicio es obligatoria.";
        } elseif (!is_numeric($precio) || $precio <= 0) {
            $error = "El precio debe ser un número mayor que cero.";
        } else {
            // **MANEJO DE LA IMAGEN (OPCIONAL)**
            $imagen_url = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png'];
                $max_size = 2 * 1024 * 1024; // 2MB

                if (in_array($_FILES['imagen']['type'], $allowed_types) && $_FILES['imagen']['size'] <= $max_size) {
                    $upload_dir = "uploads/"; // Crea esta carpeta en tu servidor
                    $filename = uniqid() . "_" . basename($_FILES['imagen']['name']);
                    $target_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_path)) {
                        $imagen_url = $target_path; // Guardar la ruta en la base de datos
                    } else {
                        $error = "Error al subir la imagen.";
                    }
                } else {
                    $error = "Formato de imagen no válido o tamaño demasiado grande.";
                }
            }

            // **INSERCIÓN EN LA BASE DE DATOS**
            if (empty($error)) {
                $sql = "INSERT INTO servicios (nombre, descripcion, precio, imagen_url, id_usuario, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen_url, $user_id);

                if ($stmt->execute()) {
                    $success = "Servicio creado exitosamente.";
                    // Redirigir al usuario al listado de servicios
                    header("Location: /servicios.php?success=1");
                    exit();
                } else {
                    $error = "Error al guardar el servicio en la base de datos: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="index2.html" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="/servicios.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <a href="/contacto.html" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <a href="/registro.html" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="/login.html" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/panel_usuario.php" class="text-gray-700 hover:text-black mr-4">Mi Panel</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Crear Nuevo Servicio</h1>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="text-green-500 mb-4"><?php echo $success; ?></p>
            <?php endif; ?>
            <form action="/crear_servicio.php" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                    <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG. Tamaño máximo: [establecer límite].</p>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Crear Servicio</button>
                    <a href="/servicios.php" class="inline-block align-baseline font-semibold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        &copy; 2025 We-Connect. Todos los derechos reservados.
    </footer>
</body>
</html>