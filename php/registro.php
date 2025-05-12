<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de registro</title>
    <!-- Aplico CSS de BOOTSTRAP -->
    <script src="https://www.paypal.com/sdk/js?client-id=AWrC9V-h8MeR2Pif0XqXYIYjnS7TsU-hIrTfp500af2-QelD_uy0wTLT-0k2irrQjV8MjFWjGmkKoVLn"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #fbc02d 0%, #ffeb3b 100%);
        }

        .registration-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .plan-card {
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-3px);
        }

        .plan-card.selected {
            border-width: 2px;
            box-shadow: 0 0 0 3px rgba(251, 192, 45, 0.6);
        }

        .register-button {
            background-color: #fbc02d;
            color: #333;
            font-weight: bold;
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }

        .register-button:hover {
            background-color: #e0ac28;
        }

        /* Estilos para los mensajes de error */
        .error-message {
            background-color: #FEE2E2;
            border: 1px solid #F87171;
            color: #DC2626;
            padding: 0.5rem;
            border-radius: 0.375rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }

        .error-message::before {
            content: '⚠️';
            margin-right: 0.5rem;
        }

        /* Estilo para inputs con error */
        .input-error {
            border-color: #DC2626 !important;
            background-color: #FEF2F2;
        }

        /* Animación para mensajes de error */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-message {
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Estilos para el contenedor de errores del servidor */
        .server-errors {
            background-color: #FEE2E2;
            border: 1px solid #F87171;
            color: #DC2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .server-errors p {
            margin: 0.25rem 0;
        }

        .plan-card {
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-3px);
        }

        .plan-card.selected {
            border-width: 2px;
            box-shadow: 0 0 0 3px rgba(251, 192, 45, 0.6);
        }

        .register-button {
            background-color: #fbc02d;
            color: #333;
            font-weight: bold;
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }

        .register-button:hover {
            background-color: #e0ac28;
        }
    </style>
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require('./util/config.php');
    ?>
</head>

<body class="flex items-center justify-center min-h-screen">

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errores = array();

        $tmpNombre = trim($_POST["nombre"]);
        $tmpUsuario = trim($_POST["usuario"]);
        $tmpEmail = trim($_POST["email"]);
        $tmpContrasena = $_POST["contrasena"];

        // Validación del nombre
        if (strlen($tmpNombre) < 2) {
            $errores[] = "El nombre debe tener al menos 2 caracteres";
        }

        // Validación del usuario
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $tmpUsuario)) {
            $errores[] = "El usuario debe tener entre 3 y 20 caracteres y solo puede contener letras, números y guiones bajos";
        }

        // Verificar si el usuario ya existe
        $checkUsuario = "SELECT * FROM usuario WHERE usuario = '$tmpUsuario'";
        $resultUsuario = $_conexion->query($checkUsuario);
        if ($resultUsuario->num_rows > 0) {
            $errores[] = "Este nombre de usuario ya está en uso";
        }

        // Verificar si el email ya existe
        $checkEmail = "SELECT * FROM usuario WHERE email = '$tmpEmail'";
        $resultEmail = $_conexion->query($checkEmail);
        if ($resultEmail->num_rows > 0) {
            $errores[] = "Este email ya está registrado";
        }

        if (empty($errores)) {
            $contrasena_cifrada = password_hash($tmpContrasena, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuario (nombre, usuario, email, contrasena) VALUES (?, ?, ?, ?)";
            $stmt = $_conexion->prepare($sql);
            $stmt->bind_param("ssss", $tmpNombre, $tmpUsuario, $tmpEmail, $contrasena_cifrada);

            if ($stmt->execute()) {
                header("location: login.php");
                exit;
            } else {
                $errores[] = "Error al registrar el usuario";
            }
        }
    }
    ?>

    <div class="registration-container max-w-2xl w-full p-8">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Crea tu Cuenta en We-Connect</h2>
        
        <!-- enctype="multipart/form-data" para que el formulario pueda leer imagenes -->
        <form id="registro-form" class="space-y-4" method="post">

            <!-- Nombre del usuario -->
            <div>
                <label for="nombre" class="block text-sm text-gray-700 mb-1">Nombre :</label>
                <input type="text" id="nombre" name="nombre" required class="w-full p-2 border rounded" />
            </div>

            <!-- Nombre de usuario alias -->
            <div>
                <label for="usuario" class="block text-sm text-gray-700 mb-1">Nombre de usuario ó alias:</label>
                <input type="text" id="usuario" name="usuario" required class="w-full p-2 border rounded" />
            </div>

            <!-- Email de usuario -->
            <div>
                <label for="email" class="block text-sm text-gray-700 mb-1">Email:</label>
                <input type="email" class="w-full p-2 border rounded" name="email" id="email">
                <?php if (isset($errorEmail)) echo "<span class='error'>$errorEmail</span>"; ?>
            </div>

            <!-- Contraseña de usuario -->
            <div>
                <label for="contrasena" class="block text-sm text-gray-700 mb-1">Contraseña:</label>
                <input type="password" class="w-full p-2 border rounded" name="contrasena" id="contrasena">
                <p class="text-sm text-gray-600 mt-1">La contraseña debe contener:
                    • Mínimo 8 caracteres
                    • Al menos una letra mayúscula
                    • Al menos una letra minúscula
                    • Al menos un número
                </p>
                <?php if (isset($errorContrasena)) echo "<span class='error'>$errorContrasena</span>"; ?>
            </div>

            <!-- Planes como tarjetas -->
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

            <!-- PayPal -->
            <div id="paypal-button-container" class="hidden mt-4"></div>

            <div>
                <input type="submit" class="register-button mt-4" value="Registrarse">
            </div>
        </form>

        <!-- Botón Volver movido al final -->
        <div class="mt-6 text-center">
            <button type="button" onclick="window.location.href='../index.php'" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Volver</button>
        </div>
    </div>
    <script>
        const planFree = document.getElementById('plan-free');
        const planPremium = document.getElementById('plan-premium');
        const paypalContainer = document.getElementById('paypal-button-container');
        const planInput = document.getElementById('plan');
        const registerButton = document.querySelector('.register-button');

        function seleccionarPlan(tipo) {
            if (tipo === 'premium') {
                planPremium.classList.add('selected');
                planFree.classList.remove('selected');
                paypalContainer.classList.remove('hidden');
                registerButton.style.display = 'none';
                planInput.value = 'premium';
            } else {
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
                            value: '5.00'
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
    </script>
</body>

</html>

<script>
    // Validación del formulario
    const form = document.getElementById('registro-form');
    const nombre = document.getElementById('nombre');
    const usuario = document.getElementById('usuario');
    const email = document.getElementById('email');
    const contrasena = document.getElementById('contrasena');

    function mostrarError(input, mensaje) {
        const formControl = input.parentElement;
        const errorDiv = formControl.querySelector('.error-message') || document.createElement('div');
        errorDiv.className = 'error-message text-red-500 text-sm mt-1';
        errorDiv.textContent = mensaje;
        if (!formControl.querySelector('.error-message')) {
            formControl.appendChild(errorDiv);
        }
    }

    function limpiarError(input) {
        const formControl = input.parentElement;
        const errorDiv = formControl.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    function validarFormulario(e) {
        let esValido = true;

        // Validar nombre (sin números)
        if (nombre.value.trim().length < 2) {
            mostrarError(nombre, 'El nombre debe tener al menos 2 caracteres');
            esValido = false;
        } else if (/\d/.test(nombre.value.trim())) {
            mostrarError(nombre, 'El nombre no puede contener números');
            esValido = false;
        } else {
            limpiarError(nombre);
        }

        // Validar usuario (mínimo 5 caracteres)
        if (!/^[a-zA-Z0-9_]{5,20}$/.test(usuario.value.trim())) {
            mostrarError(usuario, 'El nombre de usuario debe tener entre 5 y 20 caracteres y solo puede contener letras, números y guiones bajos');
            esValido = false;
        } else {
            limpiarError(usuario);
        }

        // Validar email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            mostrarError(email, 'Por favor, introduce un email válido');
            esValido = false;
        } else {
            limpiarError(email);
        }

        // Validar contraseña
        if (contrasena.value.length < 8) {
            mostrarError(contrasena, 'La contraseña debe tener al menos 8 caracteres');
            esValido = false;
        } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(contrasena.value)) {
            mostrarError(contrasena, 'La contraseña debe contener al menos una mayúscula, una minúscula y un número');
            esValido = false;
        } else {
            limpiarError(contrasena);
        }

        if (!esValido) {
            e.preventDefault();
        }
    }

    form.addEventListener('submit', validarFormulario);

    // Resto del código de PayPal y selección de plan
    // ... existing code ...
</script>