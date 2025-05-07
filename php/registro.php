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
  </style>
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );   
        require('./config.php');
    ?>
</head>
<body class="flex items-center justify-center min-h-screen">
    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $tmpNombre = $_POST["nombre"];
            $tmpAlias = $_POST["alias"];
            $tmpEmail = $_POST["email"];
            $tmpContrasena = $_POST["contrasena"];
            

            //debido a que la contraseña está cifrada tenemos que hacer su passwordHash
            $contrasena_cifrada = password_hash($tmpContrasena,PASSWORD_DEFAULT);

            //Introduzco en mi base de datos dentro de la tabla usuarios. el nombre de usuario y contraseña
            $sql = "INSERT INTO usuario (nombre, alias, email, contrasena) VALUES ('$tmpNombre', '$tmpAlias', '$tmpEmail','$contrasena_cifrada')";
            $_conexion -> query($sql);

            header("location: ./login.php");
            exit;
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

            <!-- Alias del usuario -->
            <div>
                <label for="alias" class="block text-sm text-gray-700 mb-1">Nombre de usuario ó alias:</label>
                <input type="text" id="alias" name="alias" required class="w-full p-2 border rounded" />
            </div>
        
            <!-- Email de usuario -->
            <div>
                <label for="email" class="block text-sm text-gray-700 mb-1">Email:</label>
                <input type="email" class="w-full p-2 border rounded" name="email" id="email" >
                <?php if (isset($errorEmail)) echo "<span class='error'>$errorEmail</span>"; ?>
            </div>
           
            <!-- Contraseña de usuario -->
            <div>
                <label for="contrasena" class="block text-sm text-gray-700 mb-1">Contraseña:</label>
                <input type="password" class="w-full p-2 border rounded" name="contrasena" id="contrasena" >
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
                <!-- <a href="./login.php" class="btn btn-secondary">Inicia sesión</a> -->
            </div>
        </form>
    </div>
    <script>
        const planFree = document.getElementById('plan-free');
        const planPremium = document.getElementById('plan-premium');
        const paypalContainer = document.getElementById('paypal-button-container');
        const planInput = document.getElementById('plan');
        const submitButton = document.getElementById('submit-button');

        function seleccionarPlan(tipo) {
        if (tipo === 'premium') {
            planPremium.classList.add('selected');
            planFree.classList.remove('selected');
            paypalContainer.classList.remove('hidden');
            submitButton.disabled = true;
            planInput.value = 'premium';
        } else {
            planFree.classList.add('selected');
            planPremium.classList.remove('selected');
            paypalContainer.classList.add('hidden');
            submitButton.disabled = false;
            planInput.value = 'free';
        }
        }

        planFree.addEventListener('click', () => seleccionarPlan('free'));
        planPremium.addEventListener('click', () => seleccionarPlan('premium'));

        seleccionarPlan('free');

        // PayPal button render
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
            alert('Pago completado por ' + details.payer.name.given_name);
            submitButton.disabled = false;
            });
        }
        }).render('#paypal-button-container');
  </script>
</body>
</html>
