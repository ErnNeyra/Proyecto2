<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// !!! VERIFICA Y AJUSTA ESTAS RUTAS SI ES NECESARIO para tu script en php/util/!!!
// Si tu estructura es Proyecto2/php/util/ a Proyecto2/lib/PHPMailer-master/src/
// La ruta relativa es ../../lib/PHPMailer-master/src/.
require __DIR__ . '/../../lib/PHPMailer-master/src/Exception.php'; 
require __DIR__ . '/../../lib/PHPMailer-master/src/PHPMailer.php'; 
require __DIR__ . '/../../lib/PHPMailer-master/src/SMTP.php';     


// Usar __DIR__ . '...' es una forma robusta de hacer requires relativos

// --- Código para enviar el correo de bienvenida con PHPMailer ---

// Verificamos si tenemos los datos necesarios del usuario del script de registro
if (!empty($tmpEmail) && !empty($tmpNombre)) {

    $mailBienvenida = new PHPMailer(true); // Usamos try/catch para no detener el registro

    try {
        // Configuración del servidor SMTP de Gmail (La misma que antes)
        $mailBienvenida->isSMTP();
        $mailBienvenida->Host       = 'smtp.gmail.com';
        $mailBienvenida->SMTPAuth   = true;
     
       
        $mailBienvenida->Username   = 'weconnect943@gmail.com'; // <<< --- Tu cuenta de empresa que envía
       
      
        $mailBienvenida->Password   = 'gcmc nxig bmes wtcc'; 


        $mailBienvenida->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailBienvenida->Port       = 587;

      
        $mailBienvenida->CharSet = 'UTF-8';

        // Detalles del correo de bienvenida
        // De: Tu empresa
        $mailBienvenida->setFrom('weconnect943@gmail.com', 'Bienvenido a We-Connect'); // De: Tu empresa
        // Para: El correo del usuario recién registrado
        $mailBienvenida->addAddress($tmpEmail, $tmpNombre); // Enviamos al correo del usuario registrado

        // Opcional: Configurar Reply-To si quieres que las respuestas a este correo vayan a la empresa
        $mailBienvenida->addReplyTo('weconnect943@gmail.com', 'Soporte We-Connect');


        $mailBienvenida->isHTML(false); // Puedes poner true si quieres un cuerpo HTML
        // >>> CONTENIDO DEL CORREO DE BIENVENIDA (TEXTO PLANO) <<<
        $mailBienvenida->Subject = '¡Bienvenido a We-Connect! Gracias por registrarte.'; // Asunto del correo
        $mailBienvenida->Body    = "Hola " . $tmpNombre . ",\n\n" // Usamos el nombre del usuario
                              . "¡Bienvenido a We-Connect! Estamos emocionados de que te hayas unido a nuestra plataforma.\n\n"
                              . "Ahora puedes comenzar a explorar, conectar con otros profesionales y descubrir oportunidades.\n\n"
                              . "Si tienes alguna pregunta, no dudes en contactarnos.\n\n"
                              . "Saludos cordiales,\n"
                              . "El equipo de We-Connect";


        // Enviar el correo de bienvenida
        $mailBienvenida->send();

    


    } catch (Exception $e) {
        // Si el correo de bienvenida falla al enviar
        // Registra el error en los logs del servidor para depurar
        error_log("Error al enviar correo de bienvenida a " . $tmpEmail . ": " . $e->getMessage());
       
      
    }

} else {
    // Si por alguna razón las variables del usuario no están definidas (no debería pasar si se incluye en el lugar correcto)
     error_log("Error: Variables de usuario no definidas al intentar enviar correo de bienvenida.");
}

?>