<?php
    session_start();
    session_destroy();
    header("location: ../index.php");
    exit;   //cierra el fichero y lo mata
?>