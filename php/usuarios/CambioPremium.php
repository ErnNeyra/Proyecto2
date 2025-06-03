<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar a Premium</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AWrC9V-h8MeR2Pif0XqXYIYjnS7TsU-hIrTfp500af2-QelD_uy0wTLT-0k2irrQjV8MjFWjGmkKoVLn"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../css/usuario.css">
    <link rel="icon" href="../util/img/faviconWC.png" type="image/x-icon">
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        require('../util/config.php');
        session_start();
        // Asegúrate de que el usuario está logueado
        if (!isset($_SESSION['usuario'])) {
            header("Location: ../../index.php");
            exit;
        }
        $usuario_id = $_SESSION['usuario']['id_usuario'];
    ?>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="registration-container max-w-md w-full p-8 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Hazte Premium por 9,99€/mes</h2>
        <p class="mb-4 text-center text-gray-700">
            Disfruta de todas las ventajas del plan Premium:<br>
            <span class="block mt-2 text-yellow-600 font-semibold">
                • Publica más productos<br>
                • Generador de plan de Marketing<br>
                • Generador de valor agregado<br>
                • Generador de imágenes ilimitado<br>
                • Recursos exclusivos para emprendedores
            </span>
        </p>
        <form id="premium-form" method="post">
            <input type="hidden" name="premium_paid" id="premium_paid" value="0">
            <div id="paypal-button-container" class="mb-4"></div>
        </form>
        <div class="mt-4 text-center">
            <a href="panelUsuario.php" class="text-blue-500 hover:underline">Volver a mi panel</a>
        </div>
    </div>
    <script>
        const premiumPaidInput = document.getElementById('premium_paid');
        const premiumForm = document.getElementById('premium-form');
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: { value: '5.00' }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('¡Pago completado por ' + details.payer.name.given_name + '!');
                    premiumPaidInput.value = "1";
                    premiumForm.submit();
                });
            },
            onError: function(err) {
                alert('Ocurrió un error en el proceso de pago. Por favor, intenta nuevamente.');
                console.error('Error PayPal:', err);
            }
        }).render('#paypal-button-container');
    </script>
<?php
// Procesamiento backend tras el pago
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['premium_paid']) && $_POST['premium_paid'] == "1") {
    // Actualiza el plan del usuario a premium
    $stmt = $_conexion->prepare("UPDATE usuario SET plan = 'premium' WHERE id_usuario = ?");
    $stmt->bind_param("i", $usuario_id);
    if ($stmt->execute()) {
        // Actualiza la sesión
        $_SESSION['usuario']['premium'] = 1;
        // Redirige a recursos o muestra un mensaje de éxito
        header("Location: ../recursos/recursos.php?premium=ok");
        exit;
    } else {
        echo "<div class='text-red-500 text-center mt-4'>Error al actualizar el plan. Intenta de nuevo.</div>";
    }
}
?>
</body>
</html>