<?php
    function depurar($entrada){
        $salida = htmlspecialchars($entrada);
        /* trim quita los espacios a los laterales */
        $salida = trim($salida);
        /* stripslashes quita barras laterales /\ que pueden dar problemas*/
        $salida = stripslashes($salida);
        $salida = preg_replace('!\s+!', ' ', $salida);
        return $salida;
    }
?>