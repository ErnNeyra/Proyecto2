<?php
// Las rutas aquí DEBEN SER CORRECTAS para donde hayas colocado los archivos de PHPMailer
// desde la ubicación actual de este script (php/util/).
// Basado en tu estructura (Proyecto2/php/util/ a Proyecto2/lib/PHPMailer-master/src/),
// la ruta relativa es ../../lib/PHPMailer-master/src/.
// VERIFICA QUE ESTA RUTA ES CORRECTA EN TU SERVIDOR.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// !!! VERIFICA Y AJUSTA ESTAS RUTAS SI ES NECESARIO !!!
require __DIR__ . '/../../lib/PHPMailer-master/src/Exception.php';
require __DIR__ . '/../../lib/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../../lib/PHPMailer-master/src/SMTP.php';


// Usar __DIR__ . '...' es una forma robusta de hacer requires relativos

// Verificar si la solicitud es POST y si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Obtener y sanitizar los datos del formulario
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email'])); // Correo del usuario
    $asunto = htmlspecialchars(trim($_POST['asunto']));
    $mensaje = htmlspecialchars(trim($_POST['mensaje']));

    // 2. Validar datos básicos
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        header('Location: ../contacto.php?status=error&msg=Por favor, completa todos los campos.');
        exit;
    }

    // Validar formato de email (simple, PHPMailer también lo hace)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         header('Location: ../contacto.php?status=error&msg=Formato de correo electrónico inválido.');
         exit;
    }


    // *** PRIMER CORREO: ENVIAR MENSAJE DE CONTACTO A LA EMPRESA ***

    $mailToOwner = new PHPMailer(true); // Pasar 'true' habilita las excepciones

    try {
        // Configuración del servidor SMTP de Gmail (La misma para ambos correos)
        $mailToOwner->isSMTP();
        $mailToOwner->Host       = 'smtp.gmail.com'; // Servidor SMTP de Gmail
        $mailToOwner->SMTPAuth   = true;
        // *** CREDENCIALES DE LA CUENTA QUE ENVÍA AMBOS CORREOS (TU CUENTA DE EMPRESA) ***
        // ¡¡¡Importante!!! Usa la Contraseña de Aplicación si tienes 2FA activada
        $mailToOwner->Username   = 'weconnect943@gmail.com'; // <<< --- Tu cuenta de empresa que envía ambos correos
        // !!! REEMPLAZA ESTO CON LA CONTRASEÑA REAL O LA CONTRASEÑA DE APLICACIÓN PARA weconnect943@gmail.com !!!
        // (Asegúrate que esta es la correcta puesta aquí)
        $mailToOwner->Password   = 'gcmc nxig bmes wtcc'; // <<< --- ¡¡¡ ASEGÚRATE QUE ESTA ES LA CORRECTA Contraseña/App Password para weconnect943@gmail.com !!!


        $mailToOwner->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Usar TLS
        $mailToOwner->Port       = 587; // Puerto estándar para TLS


        // Configuración opcional para depuración (debería estar deshabilitada ahora)
        // $mailToOwner->SMTPDebug = 2; // Descomentar temporalmente si necesitas ver el envío del primer correo
        // $mailToOwner->Debugoutput = 'html';


        // >>> CONFIGURAR CODIFICACIÓN DE CARACTERES A UTF-8 <<<
        $mailToOwner->CharSet = 'UTF-8';

        // Detalles del correo para el PROPIETARIO/EMPRESA
        $mailToOwner->setFrom('weconnect943@gmail.com', 'Formulario de Contacto We-Connect'); // De: Tu empresa
        $mailToOwner->addAddress('weconnect943@gmail.com'); // Para: Tu empresa (como configuramos)
        $mailToOwner->addReplyTo($email, $nombre); // Responder a: El usuario que llenó el formulario

        $mailToOwner->isHTML(false); // Enviar mensaje a la empresa como texto plano
        $mailToOwner->Subject = 'Mensaje de Contacto desde We-Connect: ' . $asunto;
        $mailToOwner->Body    = "Nombre: " . $nombre . "\n"
                              . "Correo Electrónico: " . $email . "\n"
                              . "Asunto: " . $asunto . "\n\n"
                              . "Mensaje:\n" . $mensaje;

        // 5. Enviar el primer correo (al propietario/empresa)
        $ownerMailSent = $mailToOwner->send(); // Capturamos si el envío fue exitoso (true/false)

        // Si el envío al propietario falló, lanzamos una excepción específica
        if (!$ownerMailSent) {
             throw new Exception("Failed to send email to owner: " . $mailToOwner->ErrorInfo);
        }


        // *** SEGUNDO CORREO: ENVIAR RESPUESTA AUTOMÁTICA AL USUARIO ***

        $mailToUser = new PHPMailer(true); // Crear una nueva instancia para el segundo correo

        // Configuración del servidor SMTP (La misma)
        $mailToUser->isSMTP();
        $mailToUser->Host       = 'smtp.gmail.com';
        $mailToUser->SMTPAuth   = true;
        $mailToUser->Username   = 'weconnect943@gmail.com'; // <<< --- Tu cuenta de empresa que envía
        // !!! LA MISMA CONTRASEÑA O CONTRASEÑA DE APLICACIÓN QUE ANTES !!!
        $mailToUser->Password   = 'gcmc nxig bmes wtcc'; // <<< --- ¡¡¡ LA MISMA CONTRASEÑA/APP PASSWORD PARA weconnect943@gmail.com !!!


        $mailToUser->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailToUser->Port       = 587;

         // >>> CONFIGURAR CODIFICACIÓN DE CARACTERES A UTF-8 <<<
        $mailToUser->CharSet = 'UTF-8';

        // Configuración opcional para depuración del segundo correo (descomentar si necesitas ver el envío solo de este)
        // $mailToUser->SMTPDebug = 2; // Descomentar temporalmente si necesitas ver solo el envío del segundo correo
        // $mailToUser->Debugoutput = 'html';

        // Detalles del correo para el USUARIO
        // De: Tu empresa (puedes poner solo el nombre de la empresa)
        $mailToUser->setFrom('weconnect943@gmail.com', 'Equipo de We-Connect'); // De: Tu empresa (con nombre diferente si quieres)
        // >>> IMPORTANTE: Enviamos al correo que el usuario puso en el formulario ($email) <<<
        $mailToUser->addAddress($email, $nombre); // Para: El correo del usuario que llenó el formulario

        // Opcional: Configurar Reply-To para que si el usuario responde a este auto-reply, la respuesta vaya a la empresa
        $mailToUser->addReplyTo('weconnect943@gmail.com', 'Soporte We-Connect');


        $mailToUser->isHTML(false); // Puedes poner true si quieres un cuerpo HTML más elaborado
        // >>> CONTENIDO DEL CORREO AUTOMÁTICO (TEXTO PLANO) <<<
        $mailToUser->Subject = 'Confirmación de recepción de tu mensaje - We-Connect'; // Asunto para el usuario
        $mailToUser->Body    = "Hola " . $nombre . ",\n\n" // Incluimos el nombre del usuario
                              . "Muchas gracias por contactar con nosotros a través de We-Connect.\n" // Tu mensaje de agradecimiento
                              . "Hemos recibido tu mensaje y te responderemos en menos de 24 horas.\n\n" // Tu promesa de tiempo de respuesta
                              . "Saludos cordiales,\n"
                              . "El equipo de We-Connect\n\n"
                              . "-------------------------------------------\n" // Opcional: Separador
                              . "Detalles del mensaje que enviaste (para tu referencia):\n" // Opcional: Incluir mensaje original
                              . "Asunto: " . $asunto . "\n"
                              . "Mensaje:\n" . $mensaje . "\n"
                              . "-------------------------------------------\n";


        // 6. Enviar el segundo correo (al usuario)
        $userMailSent = $mailToUser->send(); // Capturamos si el envío fue exitoso (true/false)

        // Si el envío al usuario falló, lanzamos una excepción específica
         if (!$userMailSent) {
             throw new Exception("Failed to send email to user: " . $mailToUser->ErrorInfo);
         }


        // Ambos correos se enviaron sin errores detectados por send(), redirigir con éxito
        header('Location: ../contacto.php?status=success&msg=¡Gracias por tu mensaje! Hemos recibido tu consulta y te hemos enviado un correo de confirmación.');
        exit;

    } catch (Exception $e) {
        // Si algo falla en el envío de CUALQUIERA de los correos
        // Se registrará en los logs del servidor. El usuario solo ve el mensaje genérico.
        error_log("Error al enviar correo(s) de contacto: " . $e->getMessage());

        $errorMessage = "Hubo un problema al enviar tu mensaje.";
        if (isset($mailToOwner) && $mailToOwner->ErrorInfo) {
            $errorMessage .= " ErrorEnvioProp: " . $mailToOwner->ErrorInfo;
        }
         if (isset($mailToUser) && $mailToUser->ErrorInfo) {
             $errorMessage .= " ErrorEnvioUser: " . $mailToUser->ErrorInfo;
         }


        header('Location: ../contacto.php?status=error&msg=' . urlencode($errorMessage)); // Codificar mensaje de error en la URL
        exit;
    }

} else {
    // Si se intenta acceder a este script directamente sin POST, redirigir
    header('Location: ../contacto.php');
    exit;
}
?>