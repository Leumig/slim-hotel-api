<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class LogsMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        $this->crearLog($request);

        return $response;
    }

    private function crearLog($request)
    {
        $ruta = $request->getUri()->getPath();
        $metodo = $request->getMethod();

        $accion = $this->calcularAccion($ruta, $metodo);

        if ($accion !== 'Solicitud de Token' && $accion !== 'Alta Ajuste')
        {
            $usuario = $this->obtenerUsuario($request);
        } else if ($accion !== 'Alta Ajuste') {
            $parametros = $request->getParsedBody();
            $usuario = $parametros['usuario'];
        } else {
            $usuario = $this->obtenerUsuario($request);
            return $this->crearLogAjuste($usuario);
        }

        if ($accion !== null && strlen($accion) > 2 && $usuario !== null)
        {    
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (usuario, accion, fecha_y_hora) VALUES (:usuario, :accion, NOW())");
    
            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->bindValue(':accion', $accion, PDO::PARAM_STR);
    
            $consulta->execute();
        }
    }

    private function calcularAccion($ruta, $metodo)
    {
        $retorno = 'Desconocida';

        switch ($ruta) {
            case '/usuarios/':
                if ($metodo == 'GET')
                {
                    $retorno = 'Mostrar todos los Clientes';
                } else if ($metodo == 'POST') {
                    $retorno = 'Alta Cliente';
                }
                break;
            case '/usuarios/ConsultarCliente':
                $retorno = 'Consultar Cliente';
                break;
            case '/clientes/BorrarCliente':
                $retorno = 'Borrar Cliente';
                break;
            case '/reservas/':
                if ($metodo == 'GET')
                {
                    $retorno = 'Mostrar todas las Reservas';
                } else if ($metodo == 'POST') {
                    $retorno = 'Alta Reserva';
                }
                break;
            case '/reservas/ConsultarReserva':
                $retorno = 'Consultar Reservas';
                break;
            case '/ajustes/':
                if ($metodo == 'GET')
                {
                    $retorno = 'Mostrar todos los Ajustes';
                } else if ($metodo == 'POST') {
                    $retorno = 'Alta Ajuste';
                }
                break;
            case '/gerentes/':
                if ($metodo == 'GET')
                {
                    $retorno = 'Mostrar todos los Gerentes';
                } else if ($metodo == 'POST') {
                    $retorno = 'Alta Gerente';
                }
                break;
            case '/recepcionistas/':
                if ($metodo == 'GET')
                {
                    $retorno = 'Mostrar todos los Recepcionista';
                } else if ($metodo == 'POST') {
                    $retorno = 'Alta Recepcionista';
                }
                break;
            case '/auth/login':
                if ($metodo == 'POST')
                {
                    $retorno = 'Solicitud de Token';
                }
                break;
        }

        if ($metodo == 'PUT')
        {
            $retorno = 'Modificar Cliente';
        }

        return $retorno;
    }

    private function obtenerUsuario($request)
    {
        $header = $request->getHeaderLine('Authorization');

        if (strlen($header) > 0) {
            $token = trim(explode("Bearer", $header)[1]);
    
            $data = AutentificadorJWT::ObtenerData($token);
    
            $usuario = $data->usuario;

            if (strlen($usuario) > 1)
            {
                return $usuario;
            }
        }

        return null;
    }

    private function crearLogAjuste($usuario)
    {
        if ($usuario !== null)
        {    
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs_reservas (usuario, id_ajuste, fecha_y_hora) VALUES (:usuario, :id_ajuste, NOW())");

            $id_ajuste = Ajuste::obtenerUltimaId();

            $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $consulta->bindValue(':id_ajuste', $id_ajuste, PDO::PARAM_INT);
    
            $consulta->execute();
        }
    }
}