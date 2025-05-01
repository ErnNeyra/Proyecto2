<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recopilar los datos del formulario
    $nombre_contacto = strip_tags(trim($_POST["nombre-contacto"]));
    $email_contacto = filter_var(trim($_POST["email-contacto"]), FILTER_SANITIZE_EMAIL);
    $mensaje_contacto = strip_tags(trim($_POST["mensaje-contacto"]));
    $id_producto = filter_var($_POST["id_producto"], FILTER_SANITIZE_NUMBER_INT);
    $id_emprendedor = filter_var($_POST["id_emprendedor"], FILTER_SANITIZE_NUMBER_INT);

    // Validar los datos
    if (empty($nombre_contacto) || empty($mensaje_contacto) || !filter_var($email_contacto, FILTER_VALIDATE_EMAIL)) {
        // Redirigir con un mensaje de error
        header("Location: /detalle_producto.php?id=" . $id_producto . "&error=campos_vacios");
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

    // Obtener la dirección de correo electrónico del emprendedor
    $sql_emprendedor = "SELECT email FROM usuarios WHERE id = ?";
    $stmt_emprendedor = $conn->prepare($sql_emprendedor);
    $stmt_emprendedor->bind_param("i", $id_emprendedor);
    $stmt_emprendedor->execute();
    $result_emprendedor = $stmt_emprendedor->get_result();

    if ($result_emprendedor->num_rows == 1) {
        $fila_emprendedor = $result_emprendedor->fetch_assoc();
        $email_destinatario = $fila_emprendedor["email"];

        // Asunto del correo electrónico
        $asunto = "Consulta sobre el producto/servicio ID: " . $id_producto . " desde We-Connect";

        // Cuerpo del correo electrónico
        $cuerpo_email = "Nombre del remitente: " . $nombre_contacto . "\n";
        $cuerpo_email .= "Correo electrónico del remitente: " . $email_contacto . "\n";
        $cuerpo_email .= "Mensaje:\n" . $mensaje_contacto . "\n\n";
        $cuerpo_email .= "Este mensaje fue enviado desde la página de detalles del producto/servicio en We-Connect.";

        // Encabezados del correo electrónico
        $encabezados = "From: " . $email_contacto . "\r\n";
        $encabezados .= "Reply-To: " . $email_contacto . "\r\n";
        $encabezados .= "X-Mailer: PHP/" . phpversion();

        // Enviar el correo electrónico
        $envio_exitoso = mail($email_destinatario, $asunto, $cuerpo_email, $encabezados);

        if ($envio_exitoso) {
            // Redirigir con un mensaje de éxito
            header("Location: /detalle_producto.php?id=" . $id_producto . "&mensaje=enviado");
            exit();
        } else {
            // Redirigir con un mensaje de error de envío
            header("Location: /detalle_producto.php?id=" . $id_producto . "&error=envio_fallido");
            exit();
        }

    } else {
        // No se encontró el emprendedor
        header("Location: /detalle_producto.php?id=" . $id_producto . "&error=emprendedor_no_encontrado");
        exit();
    }

    $stmt_emprendedor->close();
    $conn->close();

} else {
    // Si se intenta acceder al script por GET, redirigir
    header("Location: /detalle_producto.php?id=" . $id_producto);
    exit();
}
?>