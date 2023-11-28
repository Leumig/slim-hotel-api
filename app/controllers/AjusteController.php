<?php
require_once './models/Ajuste.php';
require_once './interfaces/IApiUsable.php';

class AjusteController extends Ajuste
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id_reserva = $parametros['id_reserva'];
        $motivo = $parametros['motivo'];
        $monto = $parametros['monto'];

        if (validarNumerico($id_reserva) && validarString($motivo, 3, 50) &&
            validarNumerico($monto, -999999, 999999) && Reserva::obtenerReserva($id_reserva) !== false)
        {
            // Creamos el ajuste
            $ajusteNuevo = new Ajuste();
            $ajusteNuevo->id_reserva = $id_reserva;
            $ajusteNuevo->motivo = $motivo;
            $ajusteNuevo->monto = $monto;

            $respuesta = $ajusteNuevo->crearAjuste();

            if (is_numeric($respuesta))
            {
                Reserva::ajustarReserva($ajusteNuevo);
                $payload = json_encode(array("mensaje" => "Ajuste creado con exito, ID: " . $respuesta));
            } else {
                $payload = json_encode(array("error" => $respuesta));
            }
        } else {
            $payload = json_encode(array("error" => 'Valores no validos o no existe la reserva'));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos ajuste por id
        $id = $args['id'];
        $ajuste = Ajuste::obtenerAjuste($id);
        $payload = $ajuste !== false?json_encode($ajuste):json_encode(array("error" => "No se encontro"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Ajuste::obtenerTodos();
        $payload = json_encode(array("listaDeAjustes" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
