<?php
require_once './models/Admin.php';
require_once './interfaces/IApiUsable.php';

class AdminController
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

        // Creamos el Admin
        $adminNuevo = new Admin();
        $adminNuevo->usuario = $usuario;
        $adminNuevo->clave = $claveHasheada;
        $adminNuevo->nombre = $nombre;
        $adminNuevo->apellido = $apellido;
        $adminNuevo->email = $email;
        $adminNuevo->pais = $pais;
        $adminNuevo->ciudad = $ciudad;
        $adminNuevo->telefono = $telefono;

        $respuesta = $adminNuevo->crearAdmin();

        if (is_numeric($respuesta))
        {
            $payload = json_encode(array("mensaje" => "Admin creado con exito, ID: " . $respuesta));
        } else {
            $payload = json_encode(array("error" => $respuesta));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Admin::obtenerTodos();
        $payload = json_encode(array("listaDeAdmins" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
