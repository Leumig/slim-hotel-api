<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $email = $parametros['email'];
        $tipo_cliente = $parametros['tipo_cliente'];
        $numero_documento = $parametros['numero_documento'];
        $pais = $parametros['pais'];
        $ciudad = $parametros['ciudad'];
        $telefono = $parametros['telefono'];

        if (isset($parametros['modalidad_pago']))
        {
            $modalidad_pago = $parametros['modalidad_pago'];
        } else {
            $modalidad_pago = 'Efectivo';
        }

        $tipo_documento = '';
        self::diferenciarTipos($tipo_cliente, $tipo_documento);

        if (validarString($nombre, 3, 20) && validarString($apellido, 3, 20) &&
            validarString($email, 5, 35, false, true) && validarString($ciudad, 4, 20) &&
            validarNumerico($numero_documento, 100000, 999999999) && validarString($pais, 4, 20) &&
            validarNumerico($telefono, 10000000, 9999999999999) && 
            ($tipo_cliente === 'Individual' || $tipo_cliente === 'Corporativo') &&
            ($tipo_documento === 'DNI' || $tipo_documento === 'LE' || $tipo_documento === 'LC' || $tipo_documento === 'PASAPORTE') && isset($_FILES['imagen']) && self::validarDniExistente($numero_documento))
            {

            // Hasheamos la contraseña
            $claveHasheada = password_hash($clave, PASSWORD_DEFAULT);

            // Creamos el usuario
            $clienteNuevo = new Usuario();
            $clienteNuevo->usuario = $usuario;
            $clienteNuevo->clave = $claveHasheada;
            $clienteNuevo->nombre = $nombre;
            $clienteNuevo->apellido = $apellido;
            $clienteNuevo->tipo_documento = $tipo_documento;
            $clienteNuevo->numero_documento = $numero_documento;
            $clienteNuevo->email = $email;
            $clienteNuevo->tipo_cliente = $tipo_cliente;
            $clienteNuevo->pais = $pais;
            $clienteNuevo->ciudad = $ciudad;
            $clienteNuevo->telefono = $telefono;
            $clienteNuevo->modalidad_pago = $modalidad_pago;

            $respuesta = $clienteNuevo->crearCliente();

            if (is_numeric($respuesta))
            {
                $clienteNuevo->guardarImagen($respuesta);
                $payload = json_encode(array("mensaje" => "Cliente creado con exito, Numero: " . $respuesta));
            } else {
                $payload = json_encode(array("error" => $respuesta));
            }
        } else {
            $payload = json_encode(array("error" => 'Valores no validos'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    private static function diferenciarTipos(&$tipo_cliente, &$tipo_documento)
    {
        $tipos = explode('-', $tipo_cliente);

        if (isset($tipos[0])) {
            $tipo_cliente = $tipos[0];
            $tipo_cliente = $tipo_cliente === 'INDI' ? 'Individual' : 'Corporativo'; 
        }
        if (isset($tipos[1])) {
            $tipo_documento = $tipos[1];
        }
    }

    private static function validarDniExistente($numero_documento)
    {
        $esValido = true;
        $clientes = Usuario::obtenerTodos();

        foreach ($clientes as $cliente) {
            if ($cliente->numero_documento === $numero_documento) {
                $esValido = false;
            }
        }

        return $esValido;
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos cliente por numero_cliente
        $numero_cliente = $args['numero_cliente'];
        $cliente = Usuario::obtenerCliente($numero_cliente);
        $payload = $cliente !== false?json_encode($cliente):json_encode(array("error" => "No se encontro"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaDeClientes" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $numero_cliente = $args['numero_cliente'];

        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $email = $parametros['email'];
        $tipo_cliente = $parametros['tipo_cliente'];
        $numero_documento = $parametros['numero_documento'];
        $pais = $parametros['pais'];
        $ciudad = $parametros['ciudad'];
        $telefono = $parametros['telefono'];

        if (isset($parametros['modalidad_pago']))
        {
            $modalidad_pago = $parametros['modalidad_pago'];
        } else {
            $modalidad_pago = 'Efectivo';
        }

        $tipo_documento = '';
        self::diferenciarTipos($tipo_cliente, $tipo_documento);

        $respuesta = Usuario::modificarCliente($numero_cliente, $nombre, $apellido, $email, $tipo_cliente, $tipo_documento, $numero_documento, $pais, $ciudad, $telefono, $modalidad_pago);
        
        $payload = json_encode(array("mensaje" => $respuesta));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $respuesta = 'No se encontro al cliente';

        $parametros = $request->getQueryParams();

        $numero_cliente = $parametros['numero_cliente'];
        $numero_documento = $parametros['numero_documento'];
        $tipo_cliente = $parametros['tipo_cliente'];
    
        $encontrado = Usuario::obtenerPorNumeros($numero_cliente, $numero_documento);
    
        if ($encontrado !== false) {
            $cliente = Usuario::obtenerCliente($numero_cliente);
            if ($cliente->tipo_cliente === $tipo_cliente)
            {
                $respuesta = Usuario::borrarCliente($cliente);
            }
        }

        $payload = json_encode(array("mensaje" => $respuesta));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarCliente($request, $response, $args)
    {
        $parametros = $request->getQueryParams();

        if (isset($parametros['numero_cliente']) && isset($parametros['numero_documento']))
        {
            $numero_cliente = $parametros['numero_cliente'];
            $numero_documento = $parametros['numero_documento'];
    
            $respuesta = Usuario::obtenerPorNumeros($numero_cliente, $numero_documento);
    
            if ($respuesta === false) {
                $respuesta = 'No se encontro al cliente';
            }
        } else {
            $respuesta = 'No se encontro al cliente';
        }

        $payload = json_encode(array("mensaje" => $respuesta));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
