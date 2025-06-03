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

    function sentence_case($entrada) {
        $salida = mb_strtolower($entrada, 'UTF-8');
        $salida = preg_replace_callback('/(^|[.!?]\s*)([a-záéíóúñ])/', function($matches) {
            return $matches[1] . mb_strtoupper($matches[2], 'UTF-8');
        }, $salida);
        return $salida;
    }
?>