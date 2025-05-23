<?php

    //CONEXION AGUSTIN
    // $servidor = "localhost";
    // $usuario = "root";
    // $contrasena = "";
    // $BBDD = "weconnectdb";

   $servidor = "localhost";
  $usuario = "u593365251_root";
  $contrasena = "Patatata12345!";
   $BBDD = "u593365251_weConnect";

    //$servidor = "localhost";
    // $usuario = "root";
    // $contrasena = "";
      //$BBDD = "We_connect";

    //Mysqli ó PDO (nosotros vamos a usar Mysqli)
    //Intenta crear una conexion con la base de datos con los siguientes parámetros y en ese orden
    //sino los recibe, muere.
    $_conexion = new Mysqli($servidor, $usuario, $contrasena, $BBDD)
        or die("Error de conexión");
?>