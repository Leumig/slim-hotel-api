<?php
require_once './models/Recepcionista.php';
require_once './interfaces/IApiUsable.php';

class RecepcionistaController
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $email = $parametros['email'];
        $pais = $parametros['pais'];
        $ciudad = $parametros['ciudad'];
        $telefono = $parametros['telefono'];

        // Hasheamos la contraseña
        $claveHasheada = password_hash($clave, PASSWORD_DEFAULT);

        // Creamos el Recepcionista
        $recepcionistaNuevo = new Recepcionista();
        $recepcionistaNuevo->usuario = $usuario;
        $recepcionistaNuevo->clave = $claveHasheada;
        $recepcionistaNuevo->nombre = $nombre;
        $recepcionistaNuevo->apellido = $apellido;
        $recepcionistaNuevo->email = $email;
        $recepcionistaNuevo->pais = $pais;
        $recepcionistaNuevo->ciudad = $ciudad;
        $recepcionistaNuevo->telefono = $telefono;

        $respuesta = $recepcionistaNuevo->crearRecepcionista();

        if (is_numeric($respuesta))
        {
            $payload = json_encode(array("mensaje" => "Recepcionista creado con exito, ID: " . $respuesta));
        } else {
            $payload = json_encode(array("error" => $respuesta));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Recepcionista::obtenerTodos();
        $payload = json_encode(array("listaDeRecepcionistas" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
