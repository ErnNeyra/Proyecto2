<?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require('../util/config.php');
    require('../util/depurar.php');

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Validar sesión de usuario
    if (!isset($_SESSION["usuario"])) {
        header("location: login.php");
        exit;
    }

    // Obtener ID del usuario desde la sesión (nunca por GET)
    $idUsuario = $_SESSION["usuario"]["id_usuario"] ?? null;
    if (!$idUsuario) {
        header("Location: ../../index.php");
        exit;
    }

    // Obtener datos actuales del usuario
    $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
    $stmt = $_conexion->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if (!$usuario) {
        echo "<p class='text-red-500'>Usuario no encontrado.</p>";
        exit;
    }

    // Variables para mostrar en el formulario
    $nombre = $usuario["nombre"];
    $email = $usuario["email"];
    $telefono = $usuario["telefono"];
    $imagenActual = $usuario["imagen"];
    $ciudad = $usuario["area_trabajo"];

    $errores = [];
    $success = "";

    // Procesar el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitización y validación
        $tmpNombre = depurar($_POST['nombre']);
        $tmpEmail = depurar($_POST['email']);
        $tmpTelefono = depurar($_POST['telefono']);
        $contrasenaActual = $_POST['contrasena_actual'] ?? '';
        $nuevaContrasena = $_POST['nueva_contrasena'] ?? '';
        $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';
        $tmpCiudad = depurar($_POST['ciudad']);
        $ciudad = $tmpCiudad;

        // Validación de nombre
        if (strlen($tmpNombre) < 2) {
            $errores[] = "El nombre debe tener al menos 2 caracteres";
        } elseif (preg_match('/\d/', $tmpNombre)) {
            $errores[] = "El nombre no puede contener números";
        } else {
            $nombre = ucwords(strtolower($tmpNombre));
        }

        // Validación de email (único, excluyendo el propio)
        if (!filter_var($tmpEmail, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Formato de email inválido";
        } else {
            $checkEmail = $_conexion->prepare("SELECT id_usuario FROM usuario WHERE email = ? AND id_usuario != ?");
            $checkEmail->bind_param("si", $tmpEmail, $idUsuario);
            $checkEmail->execute();
            if ($checkEmail->get_result()->num_rows > 0) {
                $errores[] = "Este email ya está registrado";
            } else {
                $email = strtolower($tmpEmail);
            }
        }

        // Validación de teléfono
        if (!preg_match('/^(\+|00)?\d{1,4}[\s\-\.]?\(?\d+\)?([\s\-\.]?\d+)*$/', $tmpTelefono)) {
            $errores[] = "Teléfono inválido (ej: +34612345678)";
        } else {
            $telefono = preg_replace('/[^\d+]/', '', $tmpTelefono);
            if (strpos($telefono, '0034') === 0) {
                $telefono = '+34' . substr($telefono, 4);
            } elseif (preg_match('/^[6789]\d{8}$/', $telefono)) {
                $telefono = '+34' . $telefono;
            }
        }

        // Validación de contraseña (si se cambia)
        if (!empty($nuevaContrasena)) {
            // Verificar contraseña actual
            $stmt = $_conexion->prepare("SELECT contrasena FROM usuario WHERE id_usuario = ?");
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $stmt->bind_result($hashActual);
            $stmt->fetch();
            $stmt->close();

            if (!password_verify($contrasenaActual, $hashActual)) {
                $errores[] = "Contraseña actual incorrecta";
            }

            // Validar nueva contraseña
            if (strlen($nuevaContrasena) < 8 ||
                !preg_match('/[a-z]/', $nuevaContrasena) ||
                !preg_match('/[A-Z]/', $nuevaContrasena) ||
                !preg_match('/[0-9]/', $nuevaContrasena)) {
                $errores[] = "La nueva contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número";
            } elseif ($nuevaContrasena !== $confirmarContrasena) {
                $errores[] = "Las contraseñas nuevas no coinciden";
            }
        }

        // Procesamiento de imagen
        $imagenFinal = $imagenActual;
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            $maxTamano = 2 * 1024 * 1024; // 2MB

            if (!in_array($_FILES["imagen"]["type"], $tiposPermitidos)) {
                $errores[] = "Formato de imagen no permitido (solo JPG/PNG/GIF)";
            } elseif ($_FILES["imagen"]["size"] > $maxTamano) {
                $errores[] = "La imagen supera el tamaño máximo de 2MB";
            } else {
                $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
                $nombreImagen = uniqid('perfil_', true) . '.' . $extension;
                $ubicacionFinal = "../util/img/$nombreImagen";

                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ubicacionFinal)) {
                    if (!empty($imagenActual) && basename($imagenActual) != 'usuario.png' && file_exists($imagenActual)) {
                        unlink($imagenActual);
                    }
                    $imagenFinal = $ubicacionFinal;
                } else {
                    $errores[] = "Error al subir la imagen";
                }
            }
        }

        // Actualización en la base de datos
        if (empty($errores)) {
            if (!empty($nuevaContrasena)) {
                $hashNueva = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
                $sql = "UPDATE usuario SET nombre=?, email=?, telefono=?, contrasena=?, area_trabajo=?, imagen=? WHERE id_usuario=?";
                $stmt = $_conexion->prepare($sql);
                $stmt->bind_param("ssssssi", $nombre, $email, $telefono, $hashNueva, $ciudad, $imagenFinal, $idUsuario);
            } else {
                $sql = "UPDATE usuario SET nombre=?, email=?, telefono=?, area_trabajo=?, imagen=? WHERE id_usuario=?";
                $stmt = $_conexion->prepare($sql);
                $stmt->bind_param("sssssi", $nombre, $email, $telefono, $ciudad, $imagenFinal, $idUsuario);
            }

            if ($stmt->execute()) {
                // Actualizar datos de sesión
                $_SESSION['usuario']['nombre'] = $nombre;
                $_SESSION['usuario']['email'] = $email;
                $_SESSION['usuario']['telefono'] = $telefono;
                $_SESSION['usuario']['area_trabajo'] = $ciudad;
                $_SESSION['usuario']['imagen'] = $imagenFinal;

                // Redirección para evitar reenvío de formulario
                header("Location: editarPerfil.php?success=1");
                exit;
            } else {
                $errores[] = "Error al actualizar: " . $_conexion->error;
            }
        }
    }

    // Mostrar mensaje de éxito si corresponde
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        $success = "Perfil actualizado con éxito.";
    }
?>

<!-- FORMULARIO HTML DE EJEMPLO -->
<?php if (!empty($errores)): ?>
    <div class="server-errors">
        <?php foreach ($errores as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <label>Nombre:</label>


<!-- Formulario HTML (manteniendo tu estructura actual) -->
<?php if (!empty($errores)): ?>
    <div class="server-errors">
        <?php foreach ($errores as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach ?>
    </div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/usuario.css">
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
    <!-- favicon -->
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
        .form-group input[type="password"],
        .form-group input[type="tel"] {
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
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center">
                <?php if (isset($_SESSION['usuario']['usuario'])): ?>
                    <div class="relative">
                        <button id="dropdownBtn" class="text-gray-700 hover:text-black focus:outline-none flex items-center">
                            <span class="mr-2"><?php echo htmlspecialchars($_SESSION['usuario']['usuario']); ?></span>
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </button>
                        <div id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg origin-top-right hidden">
                            <div class="py-1">
                                <a href="panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Mi Panel</a>
                                <a href="logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Cerrar Sesión</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-8">
        <div class="edit-profile-panel">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Editar Perfil</h2>

            <?php if (!empty($errores)): ?>
                <div class="server-errors">
                    <?php foreach ($errores as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="success-message mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="panelUsuario.php" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="<?php echo isset($usuario['nombre']) ? htmlspecialchars($usuario['nombre']) : ''; ?>"
                        required
                        title="El nombre debe tener entre 3 y 50 caracteres y solo puede contener letras y espacios"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo isset($usuario['email']) ? htmlspecialchars($usuario['email']) : ''; ?>"
                        required
                        pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                        title="Por favor, introduce un email válido"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input
                        type="tel"
                        id="telefono"
                        name="telefono"
                        value="<?php echo isset($usuario['telefono']) ? htmlspecialchars($usuario['telefono']) : ''; ?>"
                        required
                        pattern="^[0-9]{9,15}$"
                        title="Introduce un teléfono válido (solo números, 9 a 15 dígitos)"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                    <div class="error-message" id="error-telefono" ></span></div>
                </div>

                <div class="form-group">
                    <label for="password_actual">Contraseña Actual (requerida para cambios):</label>
                    <input
                        type="password"
                        id="password_actual"
                        name="password_actual"
                        required
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="password_nuevo">Nueva Contraseña (dejar en blanco si no desea cambiarla):</label>
                    <input
                        type="password"
                        id="password_nuevo"
                        name="password_nuevo"
                        title="La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="confirmar_password_nuevo">Confirmar Nueva Contraseña:</label>
                    <input
                        type="password"
                        id="confirmar_password_nuevo"
                        name="confirmar_password_nuevo"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                

                <div class="form-group">
                    <label for="area_trabajo">Ciudad:</label>
                    <input
                        type="text"
                        id="area_trabajo"
                        name="area_trabajo"
                        value="<?php echo isset($usuario['area_trabajo']) ? htmlspecialchars($usuario['area_trabajo']) : ''; ?>"
                        maxlength="100"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen de Perfil:</label>
                    <input
                        type="file"
                        id="imagen"
                        name="imagen"
                        accept="image/jpeg,image/png"
                        class="w-full p-3 border border-gray-300 rounded-md focus:border-yellow-500 focus:ring-yellow-500">
                    <p class="text-sm text-gray-500 mt-1">Formatos permitidos: JPG, PNG. Tamaño máximo: 5MB</p>

                    <!-- Generador de logo con IA en línea -->
                    <div id="generadorLogoInline" class="mt-4">
                        <button type="button"
                                id="generarLogo"
                                class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">
                            Generar Logo con IA
                        </button>
                        <div id="logoIAForm" class="hidden mt-4">
                            <div class="form-group">
                                <label for="logoDescripcion" class="block mb-2">Describe cómo quieres que sea tu logo:</label>
                                <textarea
                                    id="logoDescripcion"
                                    class="w-full p-3 border border-gray-300 rounded-md focus:border-yellow-500 focus:ring-yellow-500"
                                    rows="4"
                                    placeholder="Ejemplo: Un logo minimalista para una tienda de ropa, con tonos azules y blancos..."></textarea>
                            </div>
                            <button type="button"
                                    id="generarLogoBtn"
                                    class="w-full bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 mt-2">
                                Generar Logo
                            </button>
                            <div id="loadingIndicator" class="hidden flex items-center justify-center mt-4">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-500"></div>
                            </div>
                        </div>
                        <div id="logoResultado" class="hidden mt-4">
                            <div class="flex justify-center">
                                <img id="logoGenerado" class="max-w-full h-auto mb-4 rounded-lg shadow-lg" src="" alt="Logo generado">
                            </div>
                            <div class="flex space-x-2">
                                <button type="button"
                                        id="regenerarLogo"
                                        class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600">
                                    Generar Otro
                                </button>
                                <button type="button"
                                        id="guardarLogo"
                                        class="flex-1 bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600">
                                    Usar Este Logo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="hover:bg-yellow-600 transition-colors duration-300">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script>
        const dropdownBtn = document.getElementById('dropdownBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });

            // Cerrar el desplegable si se hace clic fuera
            document.addEventListener('click', (event) => {
                if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }

        document.getElementById('mejorarDescripcion').addEventListener('click', async function() {
            const descripcion = document.getElementById('descripcion').value;
            const sugerenciaDiv = document.getElementById('sugerenciaDescripcion');
            const textoSugerencia = document.getElementById('textoSugerencia');

            if (!descripcion.trim()) {
                alert('Por favor, ingrese una descripción primero.');
                return;
            }

            try {
                this.disabled = true;
                this.textContent = 'Procesando...';

                const response = await fetch('../util/mejorar_descripcion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ descripcion: descripcion })
                });

                const data = await response.json();

                if (data.success) {
                    textoSugerencia.textContent = data.success;
                    sugerenciaDiv.classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'No se pudo procesar la solicitud'));
                }
            } catch (error) {
                alert('Error al procesar la solicitud');
            } finally {
                this.disabled = false;
                this.textContent = 'Mejorar Descripción';
            }
        });

        document.getElementById('aplicarSugerencia').addEventListener('click', function() {
            const descripcion = document.getElementById('descripcion');
            const sugerencia = document.getElementById('textoSugerencia').textContent;
            descripcion.value = sugerencia;
            document.getElementById('sugerenciaDescripcion').classList.add('hidden');
        });

        // Generador de logo con IA en línea (sin modal)
        document.getElementById('generarLogo').addEventListener('click', function() {
            document.getElementById('logoIAForm').classList.remove('hidden');
            document.getElementById('logoResultado').classList.add('hidden');
            document.getElementById('logoDescripcion').value = '';
        });

        document.getElementById('generarLogoBtn').addEventListener('click', async function() {
            const descripcion = document.getElementById('logoDescripcion').value;
            const loadingIndicator = document.getElementById('loadingIndicator');

            if (!descripcion.trim()) {
                alert('Por favor, describe cómo quieres que sea tu logo.');
                return;
            }

            try {
                this.disabled = true;
                loadingIndicator.classList.remove('hidden');

                const response = await fetch('../util/generar_logo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ descripcion: descripcion })
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('logoGenerado').src = data.imageUrl;
                    document.getElementById('logoIAForm').classList.add('hidden');
                    document.getElementById('logoResultado').classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'No se pudo generar el logo'));
                }
            } catch (error) {
                alert('Error al generar el logo');
            } finally {
                this.disabled = false;
                loadingIndicator.classList.add('hidden');
            }
        });

        document.getElementById('regenerarLogo').addEventListener('click', function() {
            document.getElementById('logoResultado').classList.add('hidden');
            document.getElementById('logoIAForm').classList.remove('hidden');
        });

        document.getElementById('guardarLogo').addEventListener('click', async function() {
            const logoUrl = document.getElementById('logoGenerado').src;

            try {
                const response = await fetch(logoUrl);
                const blob = await response.blob();

                // Crear un objeto File
                const file = new File([blob], 'logo.png', { type: 'image/png' });

                // Crear un objeto DataTransfer y agregar el archivo
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                // Asignar el archivo al input de imagen
                document.getElementById('imagen').files = dataTransfer.files;

                // Ocultar el generador
                document.getElementById('logoResultado').classList.add('hidden');
                document.getElementById('logoIAForm').classList.add('hidden');

                // Opcional: Mostrar mensaje de éxito
                alert('Logo agregado correctamente. No olvides guardar los cambios.');
            } catch (error) {
                alert('Error al guardar el logo: ' + error.message);
            }
        });

        // Validación de teléfono en el front
        document.querySelector('form').addEventListener('submit', function(e) {
            const telefonoInput = document.getElementById('telefono');
            const errorTelefonoDiv = document.getElementById('error-telefono');
            let valido = true;

            if (!/^[0-9]{9,15}$/.test(telefonoInput.value.trim())) {
                errorTelefonoDiv.textContent = 'Introduce un teléfono válido (solo números, 9 a 15 dígitos)';
                telefonoInput.classList.add('border-red-500');
                valido = false;
            } else {
                errorTelefonoDiv.textContent = '';
                telefonoInput.classList.remove('border-red-500');
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>