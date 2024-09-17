<?php
require_once './models/Gerente.php';
require_once './interfaces/IApiUsable.php';

class GerenteController
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

        // Creamos el Gerente
        $gerenteNuevo = new Gerente();
        $gerenteNuevo->usuario = $usuario;
        $gerenteNuevo->clave = $claveHasheada;
        $gerenteNuevo->nombre = $nombre;
        $gerenteNuevo->apellido = $apellido;
        $gerenteNuevo->email = $email;
        $gerenteNuevo->pais = $pais;
        $gerenteNuevo->ciudad = $ciudad;
        $gerenteNuevo->telefono = $telefono;

        $respuesta = $gerenteNuevo->crearGerente();

        if (is_numeric($respuesta))
        {
            $payload = json_encode(array("mensaje" => "Gerente creado con exito, ID: " . $respuesta));
        } else {
            $payload = json_encode(array("error" => $respuesta));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Gerente::obtenerTodos();
        $payload = json_encode(array("listaDeGerentes" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
