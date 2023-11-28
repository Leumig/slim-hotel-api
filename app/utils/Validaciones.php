<?php
function validarString($cadena, $min = 1, $max = 16, $conNumeros = false, $esEmail = false) {
    $validacion = false;

    if (is_string($cadena)) {
        $cadena = trim($cadena);

        if (strlen($cadena) >= $min && strlen($cadena) <= $max) {
            if (!$conNumeros && preg_match('/^[a-zA-Z\s]*$/', $cadena) && !preg_match('/\s{2,}/', $cadena)) {
                $validacion = true;
            } elseif ($conNumeros && preg_match('/^[a-zA-Z0-9\s]*$/', $cadena) &&
                !preg_match('/\s{2,}/', $cadena)) {
                $validacion = true;
            }

            if (!$validacion && $esEmail) {
                $validacion = filter_var($cadena, FILTER_VALIDATE_EMAIL) !== false;
            }
        }
    }

    return $validacion;
}

function validarNumerico($valor, $min = 0, $max = 999999) {
    return is_numeric($valor) && $valor >= $min && $valor <= $max;
}

function validarFecha($fecha) {
    $validacion = false;

    $fechaMinima = new DateTime("1-01-1800");
    $fechaMaxima = new DateTime("31-12-3000");
    try {
        $dateTime = new DateTime($fecha);

        if ($dateTime >= $fechaMinima && $dateTime <= $fechaMaxima) {
            $validacion = true;
        }
    } catch (Exception) {
        //Ignoro la excepcion intencionalmente
        //Ya que la validacion ya esta en false
    }

    return $validacion;
}
?>