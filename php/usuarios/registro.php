<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de registro</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AWrC9V-h8MeR2Pif0XqXYIYjnS7TsU-hIrTfp500af2-QelD_uy0wTLT-0k2irrQjV8MjFWjGmkKoVLn"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/usuario.css">
    <link rel="icon" href="../util/img/.faviconWC.png " type="image/x-icon">
    <!-- favicon -->
   
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        require('../util/config.php');
        require('../util/depurar.php');

        // Definir la carpeta donde se guardarán las fotos de perfil
        $uploadDir = '../util/img/';
    ?>
</head>

<body class="flex items-center justify-center min-h-screen">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errores = array();

        $tmpNombre = depurar($_POST["nombre"]);
        $tmpUsuario = depurar($_POST["usuario"]);
        $tmpEmail = depurar($_POST["email"]);
        $tmpTelefono = depurar($_POST["telefono"]); // Nuevo campo para teléfono
        $tmpContrasena = $_POST["contrasena"];

        // Validación del nombre
        if (strlen($tmpNombre) < 2) {
            $errores[] = "El nombre debe tener al menos 2 caracteres";
        }else{
            // Validar que el nombre no contenga números
            if (preg_match('/\d/', $tmpNombre)) {
                $errores[] = "El nombre no puede contener números";
            }else{
                $nombre = $tmpNombre;
            }
        }

        // Validación del usuario
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $tmpUsuario)) {
            $errores[] = "El usuario debe tener entre 3 y 20 caracteres y solo puede contener letras, números y guiones bajos";
        }

        // Verificar si el usuario ya existe
        $stmt = $_conexion->prepare("SELECT * FROM usuario WHERE usuario = ?");
        $stmt->bind_param("s", $tmpUsuario);
        $stmt->execute();
        $resultUsuario = $stmt->get_result();
        if ($resultUsuario->num_rows > 0) {
            $errores[] = "Este usuario ya está registrado";
        }else{
            $usuario = $tmpUsuario;
        }


        // Verificar si el email ya existe
        $checkEmail = "SELECT * FROM usuario WHERE email = '$tmpEmail'";
        $resultEmail = $_conexion->query($checkEmail);
        if ($resultEmail->num_rows > 0) {
            $errores[] = "Este email ya está registrado";
        }else {
            $email = $tmpEmail;
        }

        // Validación del teléfono
        if (!preg_match('/^(\+|00)?\d{1,4}[\s\-\.]?\(?\d+\)?([\s\-\.]?\d+)*$/', $tmpTelefono)) {
            $errores[] = "Introduce un teléfono válido (solo números, 9 a 15 dígitos)";
            //EJEMPLOS VALIDOS paara españa  612345678 912345678 +34612345678 0034912345678
        }else{
            $tmpTelefono = preg_replace('/[^\d+]/', '', $tmpTelefono);

            // Si empieza por 0034, lo convierte a +34
            if (strpos($tmpTelefono, '0034') === 0) {
                $telefono = '+34' . substr($tmpTelefono, 4);
            }
            // Si empieza por 6, 7, 8 o 9 y tiene 9 dígitos, añade +34
            elseif (preg_match('/^[6789]\d{8}$/', $tmpTelefono)) {
                $telefono = '+34' . $tmpTelefono;
            }else{
                $telefono = $tmpTelefono;
            }
        }

        // Validación de la contraseña (la misma que en tu script)
        if (strlen($tmpContrasena) < 8 || !preg_match('/[a-z]/', $tmpContrasena) || !preg_match('/[A-Z]/', $tmpContrasena) || !preg_match('/[0-9]/', $tmpContrasena)) {
            $errores[] = "La contraseña debe tener al menos 8 caracteres y contener al menos una mayúscula, una minúscula y un número";
        }else{
            $contrasena = $tmpContrasena;
        }

        // Procesamiento de la foto de perfil
        $fotoPerfil = '';
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = basename($_FILES['foto_perfil']['name']);
            $rutaArchivo = $uploadDir . uniqid() . '_' . $nombreArchivo;
            $rutaCompleta = __DIR__ . '/' . $rutaArchivo;

            // Validar el tipo de archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['foto_perfil']['type'], $tiposPermitidos)) {
                // Validar el tamaño del archivo
                $maxTamano = 2 * 1024 * 1024; // 2MB
                if ($_FILES['foto_perfil']['size'] <= $maxTamano) {
                    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $rutaCompleta)) {
                        $fotoPerfil = $rutaArchivo; // Guardar la ruta relativa
                    } else {
                        $errores[] = "Error al subir la foto de perfil";
                    }
                } else {
                    $errores[] = "La foto de perfil es demasiado grande (máximo 2MB)";
                }
            } else {
                $errores[] = "Formato de foto de perfil no permitido. Solo se aceptan JPEG, PNG y GIF";
            }
        } else {
            // Si no se subió ninguna foto, asignamos la ruta por defecto
            $fotoPerfil = '../util/img/usuario.jpg';
        }

             if (empty($errores)) {
                $contrasena_cifrada = password_hash($contrasena, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuario (nombre, usuario, email, telefono, contrasena, foto_perfil) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $_conexion->prepare($sql);
                $stmt->bind_param("ssssss", $nombre, $usuario, $email, $telefono, $contrasena_cifrada, $fotoPerfil);

                if ($stmt->execute()) {
                // Obtener el ID del usuario recién registrado
                    $user_id = $_conexion->insert_id;

              
                require '../util/send_welcome_email.php';
                // *** ================================================================= ***


                // Recuperar la información del usuario para la sesión (esto ya lo tienes)
                    $sql_sesion = "SELECT id_usuario, usuario, nombre, email, telefono, foto_perfil FROM usuario WHERE id_usuario = ?";
                    $stmt_sesion = $_conexion->prepare($sql_sesion);
                    $stmt_sesion->bind_param("i", $user_id);
                    $stmt_sesion->execute();
                    $resultado_sesion = $stmt_sesion->get_result();
                    if ($row_sesion = $resultado_sesion->fetch_assoc()) {
                        // Iniciar la sesión y guardar la información del usuario (ya lo tienes)
                        session_start();
                        $_SESSION['usuario'] = array(
                            'id_usuario' => $row_sesion['id_usuario'],
                            'usuario' => $row_sesion['usuario'], // Asegúrate de usar la columna correcta
                            'nombre' => $row_sesion['nombre'],
                            'email' => $row_sesion['email'],
                            'telefono' => $row_sesion['telefono'],
                            'foto_perfil' => $row_sesion['foto_perfil']
                        );
                        // Redirección al index.php después del registro: (ya lo tienes)
                        header("location: ../../index.php");
                        exit;
                    } else {
                        $errores[] = "Error al iniciar sesión después del registro";
                    }
            } else {
                $errores[] = "Error al registrar el usuario en la base de datos";
            }
        }

        // Mostrar errores si existen
        if (!empty($errores)) {
            echo '<div class="server-errors">';
            foreach ($errores as $error) {
                echo '<p>' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
    }
    ?>

    <div class="registration-container max-w-2xl w-full p-8">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Crea tu Cuenta en We-Connect</h2>

        <form id="registro-form" class="space-y-4" method="post" enctype="multipart/form-data">

            <div>
                <label for="nombre" class="block text-sm text-gray-700 mb-1">Nombre :</label>
                <input type="text" id="nombre" name="nombre" required class="w-full p-2 border rounded" />
                <div class="error-message" id="error-nombre"></div>
            </div>

            <div>
                <label for="usuario" class="block text-sm text-gray-700 mb-1">Nombre de usuario ó alias:</label>
                <input type="text" id="usuario" name="usuario" required class="w-full p-2 border rounded" />
                <div class="error-message" id="error-usuario"></div>
            </div>

            <div>
                <label for="email" class="block text-sm text-gray-700 mb-1">Email:</label>
                <input type="email" class="w-full p-2 border rounded" name="email" id="email" required>
                <div class="error-message" id="error-email"></div>
            </div>

            <div>
                <label for="telefono" class="block text-sm text-gray-700 mb-1">Teléfono:</label>
                <input type="tel" class="w-full p-2 border rounded" name="telefono" id="telefono" required pattern="^[0-9]{9,15}$" placeholder="Ej: 612345678" />
                <div class="error-message" id="error-telefono"></div>
            </div>

            <div>
                <label for="contrasena" class="block text-sm text-gray-700 mb-1">Contraseña:</label>
                <input type="password" class="w-full p-2 border rounded" name="contrasena" id="contrasena" required>
                <p class="text-sm text-gray-600 mt-1">La contraseña debe contener:
                    • Mínimo 8 caracteres
                    • Al menos una letra mayúscula
                    • Al menos una letra minúscula
                    • Al menos un número
                </p>
                <div class="error-message" id="error-contrasena"></div>
            </div>

            <div>
                <label for="foto_perfil" class="block text-sm text-gray-700 mb-1">Foto de perfil (Opcional):</label>
                <input type="file" class="w-full p-2 border rounded" name="foto_perfil" id="foto_perfil" accept="image/*">
                <p class="text-xs text-gray-500 mt-1">Formatos permitidos: JPEG, PNG, GIF. Tamaño máximo: 2MB.</p>
                <div class="error-message" id="error-foto_perfil"></div>
            </div>

            <div class="mt-6">
                <p class="mb-2 font-medium text-gray-700">Selecciona un plan:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="plan-free" class="plan-card border border-gray-300 bg-gray-50 p-4 rounded cursor-pointer">
                        <h3 class="text-lg font-semibold text-gray-700">Plan Gratuito</h3>
                        <ul class="text-sm text-gray-600 list-disc pl-5 mt-2">
                            <li>Acceso limitado</li>
                            <li>Perfil básico</li>
                            <li>Mensajes limitados</li>
                        </ul>
                    </div>
                    <div id="plan-premium" class="plan-card border border-yellow-400 bg-yellow-100 p-4 rounded cursor-pointer">
                        <h3 class="text-lg font-semibold text-yellow-800">Plan Premium - $5 USD</h3>
                        <ul class="text-sm text-gray-700 list-disc pl-5 mt-2">
                            <li>Acceso completo</li>
                            <li>Perfil destacado</li>
                            <li>Mensajes ilimitados</li>
                            <li>Eventos exclusivos</li>
                        </ul>
                    </div>
                </div>
                <input type="hidden" id="plan" name="plan" value="free" />
            </div>

            <div id="paypal-button-container" class="hidden mt-4"></div>

            <div>
                <button type="submit" class="register-button mt-4">Registrarse</button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <button type="button" onclick="window.location.href='../../index.php'" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Volver</button>
        </div>
    </div>
    <script>
        const planFree = document.getElementById('plan-free');
        const planPremium = document.getElementById('plan-premium');
        const paypalContainer = document.getElementById('paypal-button-container');
        const planInput = document.getElementById('plan');
        const registerButton = document.querySelector('.register-button');
        const registroForm = document.getElementById('registro-form');
        const nombreInput = document.getElementById('nombre');
        const usuarioInput = document.getElementById('usuario');
        const emailInput = document.getElementById('email');
        const telefonoInput = document.getElementById('telefono'); // Nuevo input para teléfono
        const contrasenaInput = document.getElementById('contrasena');
        const fotoPerfilInput = document.getElementById('foto_perfil'); // Nuevo input
        const errorNombreDiv = document.getElementById('error-nombre'); // Nuevos divs de error
        const errorUsuarioDiv = document.getElementById('error-usuario');
        const errorEmailDiv = document.getElementById('error-email');
        const errorTelefonoDiv = document.getElementById('error-telefono'); // Nuevo div de error para teléfono
        const errorContrasenaDiv = document.getElementById('error-contrasena');
        const errorFotoPerfilDiv = document.getElementById('error-foto_perfil');

        function seleccionarPlan(tipo) {
            if (tipo === 'premium') {
                planPremium.classList.add('selected');
                planFree.classList.remove('selected');
                paypalContainer.classList.remove('hidden');
                registerButton.style.display = 'none';
                planInput.value = 'premium';} else {
                planFree.classList.add('selected');
                planPremium.classList.remove('selected');
                paypalContainer.classList.add('hidden');
                registerButton.style.display = 'block';
                planInput.value = 'free';
            }
        }

        planFree.addEventListener('click', () => seleccionarPlan('free'));
        planPremium.addEventListener('click', () => seleccionarPlan('premium'));

        // Inicializar con plan gratuito
        seleccionarPlan('free');

        // Configuración de PayPal
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '1.00'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('¡Pago completado por ' + details.payer.name.given_name + '!');
                    registerButton.style.display = 'block';
                    planInput.value = 'premium_paid';
                });
            },
            onError: function(err) {
                alert('Ocurrió un error en el proceso de pago. Por favor, intenta nuevamente.');
                console.error('Error PayPal:', err);
            }
        }).render('#paypal-button-container');

        function mostrarError(input, mensaje, errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.classList.add('visible');
            input.classList.add('input-error');
        }

        function limpiarError(input, errorDiv) {
            errorDiv.textContent = '';
            errorDiv.classList.remove('visible');
            input.classList.remove('input-error');
        }

        // Limpiar todos los errores al cargar la página
        window.addEventListener('load', function() {
            const errorDivs = document.querySelectorAll('.error-message');
            const inputs = document.querySelectorAll('input');
            
            errorDivs.forEach(div => {
                div.classList.remove('visible');
            });
            
            inputs.forEach(input => {
                input.classList.remove('input-error');
            });
        });

        function validarFormulario(e) {
            let esValido = true;

            // Validar nombre (sin números)
            if (nombreInput.value.trim().length < 2) {
                mostrarError(nombreInput, 'El nombre debe tener al menos 2 caracteres', errorNombreDiv);
                esValido = false;
            } else if (/\d/.test(nombreInput.value.trim())) {
                mostrarError(nombreInput, 'El nombre no puede contener números', errorNombreDiv);
                esValido = false;
            } else {
                limpiarError(nombreInput, errorNombreDiv);
            }

            // Validar usuario (mínimo 3 caracteres)
            if (!/^[a-zA-Z0-9_]{3,20}$/.test(usuarioInput.value.trim())) {
                mostrarError(usuarioInput, 'El nombre de usuario debe tener entre 3 y 20 caracteres y solo puede contener letras, números y guiones bajos', errorUsuarioDiv);
                esValido = false;
            } else {
                limpiarError(usuarioInput, errorUsuarioDiv);
            }

            // Validar email
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                mostrarError(emailInput, 'Por favor, introduce un email válido', errorEmailDiv);
                esValido = false;
            } else {
                limpiarError(emailInput, errorEmailDiv);
            }

            // Validar teléfono (solo números, 9 a 15 dígitos)
            if (!/^[0-9]{9,15}$/.test(telefonoInput.value.trim())) {
                mostrarError(telefonoInput, 'Introduce un teléfono válido (solo números, 9 a 15 dígitos)', errorTelefonoDiv);
                esValido = false;
            } else {
                limpiarError(telefonoInput, errorTelefonoDiv);
            }

            // Validar contraseña
            if (contrasenaInput.value.length < 8) {
                mostrarError(contrasenaInput, 'La contraseña debe tener al menos 8 caracteres', errorContrasenaDiv);
                esValido = false;
            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(contrasenaInput.value)) {
                mostrarError(contrasenaInput, 'La contraseña debe contener al menos una mayúscula, una minúscula y un número', errorContrasenaDiv);
                esValido = false;
            } else {
                limpiarError(contrasenaInput, errorContrasenaDiv);
            }

            // Validar foto de perfil (opcional)
            if (fotoPerfilInput.files.length > 0) {
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (fotoPerfilInput.files[0].size > maxSize) {
                    mostrarError(fotoPerfilInput, 'La foto de perfil es demasiado grande (máximo 2MB)', errorFotoPerfilDiv);
                    esValido = false;
                } else {
                    limpiarError(fotoPerfilInput, errorFotoPerfilDiv);
                }
            }

            if (!esValido) {
                e.preventDefault();
            }
        }

        registroForm.addEventListener('submit', validarFormulario);
    </script>
</body>
</html>