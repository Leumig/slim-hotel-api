<?php
require_once './models/Reserva.php';
require_once './interfaces/IApiUsable.php';

class ReservaController extends Reserva implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $tipo_cliente = $parametros['tipo_cliente'];
        $numero_cliente = $parametros['numero_cliente'];
        $tipo_habitacion = $parametros['tipo_habitacion'];
        $fecha_entrada = $parametros['fecha_entrada'];
        $fecha_salida = $parametros['fecha_salida'];
        $importe = $parametros['importe'];

        if (($tipo_cliente === "Individual" || $tipo_cliente === "Corporativo") &&
            ($tipo_habitacion === "Simple" || $tipo_habitacion === "Doble" || $tipo_habitacion === "Suite") &&
            validarNumerico($importe) && validarFecha($fecha_entrada) && validarFecha($fecha_salida))
        {
            if (Usuario::obtenerCliente($numero_cliente) !== false)
            {
                // Creamos la reserva
                $reservaNueva = new Reserva();
                $reservaNueva->tipo_cliente = $tipo_cliente;
                $reservaNueva->numero_cliente = $numero_cliente;
                $reservaNueva->tipo_habitacion = $tipo_habitacion;
                $reservaNueva->fecha_entrada = $fecha_entrada;
                $reservaNueva->fecha_salida = $fecha_salida;
                $reservaNueva->importe = $importe;

                $respuesta = $reservaNueva->crearReserva();

                if (is_numeric($respuesta))
                {
                    $reservaNueva->guardarImagen($respuesta);
                    $payload = json_encode(array("mensaje" => "Reserva creada con exito, ID: " . $respuesta));
                } else {
                    $payload = json_encode(array("error" => $respuesta));
                }
            } else {
                $payload = json_encode(array("error" => 'No se encontro al cliente'));
            }

        } else {
            $payload = json_encode(array("error" => 'Valores no validos'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos reserva por id
        $id = $args['id'];
        $reserva = Reserva::obtenerReserva($id);
        $payload = $reserva !== false?json_encode($reserva):json_encode(array("error" => "No se encontro"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Reserva::obtenerTodos();
        $payload = json_encode(array("listaDeReservas" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $id = $args['id'];
        $tipo_cliente = $parametros['tipo_cliente'];
        $numero_cliente = $parametros['numero_cliente'];
        $tipo_habitacion = $parametros['tipo_habitacion'];
        $fecha_entrada = $parametros['fecha_entrada'];
        $fecha_salida = $parametros['fecha_salida'];
        $importe = $parametros['importe'];

        $respuesta = Reserva::modificarReserva($id, $tipo_cliente, $numero_cliente, $tipo_habitacion, $fecha_entrada, $fecha_salida, $importe);
        
        $payload = json_encode(array("mensaje" => $respuesta));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        $id = $args['id'];
        $numero_cliente = $parametros['numero_cliente'];
        $numero_documento = $parametros['numero_documento'];

        $respuesta = Reserva::cancelarReserva($id, $numero_cliente, $numero_documento);
        $payload = json_encode(array("mensaje" => $respuesta));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarReserva($request, $response, $args)
    {
        $respuesta = 'Ocurrio un error';

        $parametros = $request->getQueryParams();
        
        $consulta = $parametros['consulta'];
        $tipo_habitacion = isset($parametros['tipo_habitacion']) ? $parametros['tipo_habitacion'] : null;
        $fecha = isset($parametros['fecha']) ? $parametros['fecha'] : null;
        $numero_cliente = isset($parametros['numero_cliente']) ? $parametros['numero_cliente'] : null;
        $fechaMin = isset($parametros['fechaMin']) ? $parametros['fechaMin'] : null;
        $fechaMax = isset($parametros['fechaMax']) ? $parametros['fechaMax'] : null;
        $tipo_cliente = isset($parametros['tipo_cliente']) ? $parametros['tipo_cliente'] : null;
        $modalidad_pago = isset($parametros['modalidad_pago']) ? $parametros['modalidad_pago'] : null;

        $lista = Reserva::obtenerTodos();

        switch ($consulta) {
            case 'A':
                if ($tipo_habitacion !== null && $fecha !== null)
                {
                    $respuesta = Reserva::listarPorTipoYFecha($tipo_habitacion, $lista, $fecha);
                } else if ($tipo_habitacion !== null)
                {
                    $respuesta = Reserva::listarPorTipoYFecha($tipo_habitacion, $lista);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;
            case 'B':
                if ($numero_cliente !== null)
                {
                    $respuesta = Reserva::listarPorCliente($numero_cliente);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'C':
                if ($fechaMin !== null && $fechaMax !== null)
                {
                    $respuesta = Reserva::listarEntreFechas($fechaMin, $fechaMax);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'D':
                if ($tipo_habitacion !== null)
                {
                    $respuesta = Reserva::listarPorTipoHabitacion($tipo_habitacion);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'E':
                if ($tipo_cliente !== null && $fecha !== null)
                {
                    $respuesta = Reserva::listarPorTipoYFechaCanceladas($tipo_cliente, $lista, $fecha);
                } else if ($tipo_cliente !== null)
                {
                    $respuesta = Reserva::listarPorTipoYFechaCanceladas($tipo_cliente, $lista);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'F':
                if ($numero_cliente !== null)
                {
                    $respuesta = Reserva::listarPorCliente($numero_cliente, true);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'G':
                if ($fechaMin !== null && $fechaMax !== null)
                {
                    $respuesta = Reserva::listarEntreFechas($fechaMin, $fechaMax, true);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'H':
                if ($tipo_cliente !== null)
                {
                    $respuesta = Reserva::listarPorTipoCliente($tipo_cliente);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'I':
                if ($numero_cliente !== null)
                {
                    $respuesta = Reserva::listarPorCliente($numero_cliente, false, true);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            case 'J':
                if ($modalidad_pago !== null)
                {
                    $respuesta = Reserva::listarPorModalidadPago($modalidad_pago);
                } else {
                    $respuesta = 'Parametros no validos';
                }
                break;            
            default:
                $respuesta = 'Esa consulta no existe. Consultas: A-J';
                break;
        }

        $payload = json_encode(array("mensaje" => $respuesta));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}