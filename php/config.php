
 <?php
    $servidor = "localhost";
    $usuario = "root";
    $contrasena = "";
    $BBDD = "We_connect";

    $_conexion = new Mysqli($servidor, $usuario, $contrasena, $BBDD) or die("Error de conexión");
?>
