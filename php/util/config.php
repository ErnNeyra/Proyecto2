<?php

    //CONEXION AGUSTIN
    $servidor = "localhost";
    $usuario = "root";
<<<<<<< HEAD
    $contrasena = "";
    $BBDD = "We_connect";
=======
    $contrasena = "123456";
    $BBDD = "weconnectdb";

    /*$servidor = "localhost";
    $usuario = "u593365251_root";
    $contrasena = "Patatata12345!";
    $BBDD = "u593365251_weConnect";*/
>>>>>>> a43297c6b0f6698b1c4ae256868c08c9cf9f90e2

    //Mysqli ó PDO (nosotros vamos a usar Mysqli)
    //Intenta crear una conexion con la base de datos con los siguientes parámetros y en ese orden
    //sino los recibe, muere.
    $_conexion = new Mysqli($servidor, $usuario, $contrasena, $BBDD)
        or die("Error de conexión");
?>