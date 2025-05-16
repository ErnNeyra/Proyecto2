<?php
// Las rutas aquí DEBEN SER CORRECTAS para donde hayas colocado los archivos de PHPMailer
// Si PHPMailer está en la raíz del proyecto (ej. PHPMailer/src/) y este script está en php/util/,
// necesitas subir dos directorios (../../) para llegar a la raíz.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../../path/to/PHPMailer/src/Exception.php'; // <<-- AJUSTA ESTA RUTA
require __DIR__ . '/../../../path/to/PHPMailer/src/PHPMailer.php'; // <<-- AJUSTA ESTA RUTA
require __DIR__ . '/../../../path/to/PHPMailer/src/SMTP.php';     // <<-- AJUSTA ESTA RUTA


// Usar __DIR__ es más seguro para rutas relativas dentro de scripts PHP

// Verificar si la solicitud es POST y si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Obtener y sanitizar los datos del formulario
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email']));
    $asunto = htmlspecialchars(trim($_POST['asunto']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));

    // 2. Validar datos básicos
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        // Redirigir de vuelta al formulario de contacto (subir un nivel para llegar a php/)
        header('Location: ../contacto.php?status=error&msg=Por favor, completa todos los campos.');
        exit;
    }

    // Validar formato de email (simple, PHPMailer también lo hace)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         // Redirigir de vuelta al formulario de contacto
         header('Location: ../contacto.php?status=error&msg=Formato de correo electrónico inválido.');
         exit;
    }


    // 3. Configurar PHPMailer
    $mail = new PHPMailer(true); // Pasar 'true' habilita las excepciones

    try {
        // Configuración del servidor SMTP de Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;
        // *** REEMPLAZA CON TUS CREDENCIALES DE GMAIL ***
        // ¡¡¡Importante!!! Usa una Contraseña de Aplicación si tienes 2FA activada (MUCHO MÁS SEGURO)
        $mail->Username   = 'tu_correo_de_gmail@gmail.com'; // Tu dirección de correo de Gmail que enviará
        $mail->Password   = 'tu_contraseña_o_contraseña_de_aplicacion'; // Tu contraseña de Gmail o la Contraseña de Aplicación

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar TLS
        $mail->Port       = 587; // Puerto estándar para TLS

        // Configuración opcional para depuración (descomentar si tienes problemas)
        // $mail->SMTPDebug = 2; // Habilitar salida de depuración detallada
        // $mail->Debugoutput = 'html'; // Mostrar salida de depuración en HTML

        // 4. Configurar los detalles del correo
        // Establecer el remitente del correo (normalmente la cuenta de Gmail que usas para enviar)
        $mail->setFrom('tu_correo_de_gmail@gmail.com', 'Formulario de Contacto We-Connect'); // Usar tu Gmail como remitente

        // Añadir la dirección a la que quieres que lleguen los mensajes del formulario
        $mail->addAddress('tu_direccion_de_destino@ejemplo.com'); // *** REEMPLAZA con la dirección que recibe los mensajes ***

        // Configurar Reply-To para poder responder directamente al email del usuario que llenó el formulario
        $mail->addReplyTo($email, $nombre);

        $mail->isHTML(false); // No enviar como HTML, solo texto plano
        $mail->Subject = 'Mensaje de Contacto desde We-Connect: ' . $asunto;
        $mail->Body    = "Nombre: " . $nombre . "\n"
                       . "Correo Electrónico: " . $email . "\n"
                       . "Asunto: " . $asunto . "\n\n"
                       . "Mensaje:\n" . $mensaje;

        // 5. Enviar el correo
        $mail->send();

        // 6. Redirigir de vuelta al formulario de contacto con un mensaje de éxito
        // La ruta es ../contacto.php porque estamos en php/util/
        header('Location: ../contacto.php?status=success&msg=¡Gracias por tu mensaje! Nos pondremos en contacto contigo pronto.');
        exit;

    } catch (Exception $e) {
        // 7. En caso de error, redirigir con un mensaje de error
        // Loggear el error detallado en los logs del servidor (IMPORTANTE para depurar)
        error_log("Error al enviar correo de contacto: " . $mail->ErrorInfo);
        // Redirigir de vuelta al formulario de contacto con error
        header('Location: ../contacto.php?status=error&msg=Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.');
        exit;
    }

} else {
    // Si se intenta acceder a este script directamente sin POST, redirigir
    header('Location: ../contacto.php');
    exit;
}
?>