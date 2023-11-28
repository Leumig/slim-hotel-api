<?php

class Reserva
{
    public $id;
    public $tipo_cliente;
    public $numero_cliente;
    public $tipo_habitacion;
    public $fecha_entrada;
    public $fecha_salida;
    public $importe;
    public $imagen;
    public $estado;
    public $fecha_alta;
    public $fecha_baja;

    public function crearReserva()
    {
        $retorno = 'Error al obtener el ultimo ID insertado';

        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO reservas (tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, estado, fecha_alta) VALUES (:tipo_cliente, :numero_cliente, :tipo_habitacion, :fecha_entrada, :fecha_salida, :importe, :estado, NOW())");

            $estado = 'Activo';
            $consulta->bindValue(':tipo_cliente', $this->tipo_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':numero_cliente', $this->numero_cliente, PDO::PARAM_INT);
            $consulta->bindValue(':tipo_habitacion', $this->tipo_habitacion, PDO::PARAM_STR);
            $consulta->bindValue(':fecha_entrada', $this->fecha_entrada);
            $consulta->bindValue(':fecha_salida', $this->fecha_salida);
            $consulta->bindValue(':importe', $this->importe);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            
            $consulta->execute();
            $retorno = $objAccesoDatos->obtenerUltimoId();
        } catch (PDOException $e) {
            $retorno = 'Error al ejecutar la consulta: ' . $e->getMessage();
        }

        return $retorno;
    }

    public function guardarImagen($id) {
        if (isset($_FILES['imagen']))
        {
            $nombreImagen = $this->tipo_cliente . $this->numero_cliente . $id . '.jpg';
            $this->imagen = $nombreImagen;

            $destino = 'img/ImagenesDeReservas2023/' . $nombreImagen;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);

            if (strlen($nombreImagen) > 4)
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta("UPDATE reservas SET imagen = :imagen WHERE id = :id");

                $consulta->bindValue(':imagen', $nombreImagen);
                $consulta->bindValue(':id', $id);

                $consulta->execute();
            }
        } else {
            $nombreImagen = 'N/A';
        }

        return $nombreImagen;
    }

    public static function obtenerTodos($incluirEliminados = false)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        if (!$incluirEliminados)
        {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta FROM reservas WHERE estado != 'Cancelada'");
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta, fecha_baja FROM reservas");
        }
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }

    public static function obtenerReserva($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta FROM reservas WHERE id = :id AND estado != 'Cancelada'");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Reserva');
    }

    public static function modificarReserva($id, $tipo_cliente, $numero_cliente, $tipo_habitacion,      $fecha_entrada, $fecha_salida, $importe)
    {
        $retorno = 'Error al modificar Reserva';

        try {
            if (self::obtenerReserva($id) !== false)
            {
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE reservas SET tipo_cliente = :tipo_cliente, numero_cliente = :numero_cliente, tipo_habitacion = :tipo_habitacion, fecha_entrada = :fecha_entrada, fecha_salida = :fecha_salida, importe = :importe WHERE id = :id");
                
                $consulta->bindValue(':tipo_cliente', $tipo_cliente, PDO::PARAM_STR);
                $consulta->bindValue(':numero_cliente', $numero_cliente, PDO::PARAM_INT);
                $consulta->bindValue(':tipo_habitacion', $tipo_habitacion, PDO::PARAM_STR);
                $consulta->bindValue(':fecha_entrada', $fecha_entrada);
                $consulta->bindValue(':fecha_salida', $fecha_salida);
                $consulta->bindValue(':importe', $importe);
                $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                $consulta->execute();

                $retorno = 'Reserva modificada con exito, ID: ' . $id;
            } else {
                $retorno = 'No se encontro la reserva';
            }
        } catch (PDOException $e) {
            $retorno = 'Error al ejecutar la consulta: ' . $e->getMessage();
        }

        return $retorno;
    }

    public static function cancelarReserva($id, $numero_cliente, $numero_documento)
    {
        $retorno = 'Error al cancelar Reserva';

        try {
            if (self::obtenerReserva($id) !== false && Usuario::validarClienteExiste($numero_cliente,           $numero_documento))
            {
                $reserva = self::obtenerReserva($id);
                if ($reserva->numero_cliente == $numero_cliente)
                {
                    $objAccesoDato = AccesoDatos::obtenerInstancia();
                    $consulta = $objAccesoDato->prepararConsulta("UPDATE reservas SET estado = :estado, fecha_baja = NOW() WHERE id = :id AND estado != 'Cancelada'");

                    $estado = 'Cancelada';
                    $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
                    $consulta->bindValue(':id', $id, PDO::PARAM_INT);
                    $consulta->execute();
                    $retorno = 'Reserva cancelada con exito, ID: ' . $id;
                } else {
                    $retorno = 'Esa reserva no es de ese cliente';
                }
            } else {
                $retorno = 'No se encontro la reserva o el cliente';
            }
        } catch (PDOException $e) {
            $retorno = 'Error al ejecutar la consulta: ' . $e->getMessage();
        }

        return $retorno;
    }

    public static function ajustarReserva($ajusteNuevo)
    {
        $reservaAjustada = self::obtenerReserva($ajusteNuevo->id_reserva);
        
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE reservas SET estado = :estado, importe = :importe WHERE id = :id");

        $estado = 'Ajustada por: ' . $ajusteNuevo->motivo . ' (' . $ajusteNuevo->monto . ')';
        $nuevoImporte = $reservaAjustada->importe + $ajusteNuevo->monto;

        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $nuevoImporte, PDO::PARAM_STR);
        $consulta->bindValue(':id', $ajusteNuevo->id_reserva, PDO::PARAM_INT);
        $consulta->execute();
    }

    static function listarPorTipoYFecha($tipo_habitacion, $lista, $fecha = null) {
        $respuesta = "No se pudo realizar la consulta";

        if ($tipo_habitacion == "Simple" || $tipo_habitacion == "Doble" || $tipo_habitacion == "Suite") {
            if ($fecha !== null) {
                $fecha = DateTime::createFromFormat("Y-m-d", $fecha);
            } else {
                $fecha = new DateTime("yesterday");
            }
            
            $listaFiltrada = [];

            foreach ($lista as $reserva) {
                $fechaReservaE = DateTime::createFromFormat("Y-m-d", $reserva->fecha_entrada);
                $fechaReservaS = DateTime::createFromFormat("Y-m-d", $reserva->fecha_salida);

                if ($reserva->tipo_habitacion == $tipo_habitacion && 
                $fechaReservaE <= $fecha && $fechaReservaS >= $fecha) {
                    array_push($listaFiltrada, $reserva);
                }
            }
            
            $respuesta = array_reduce($listaFiltrada, fn($acum, $r) => $acum + $r->importe, 0);
        }

        return $respuesta;
    }

    public static function listarPorCliente($numero_cliente, $soloCancelaciones = false, $incluirCancelaciones = false)
    {
        $respuesta = 'No se encontraron coincidencias';
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        
        if (Usuario::obtenerCliente($numero_cliente) !== false && !$soloCancelaciones && !$incluirCancelaciones)
        {
            $consulta = $objAccesoDato->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta FROM reservas WHERE estado != 'Cancelada' AND numero_cliente = :numero_cliente");

            $consulta->bindValue(':numero_cliente', $numero_cliente, PDO::PARAM_INT);
            $consulta->execute();

            $respuesta = $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
        } else if (Usuario::obtenerCliente($numero_cliente) !== false && $soloCancelaciones) {
            $consulta = $objAccesoDato->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta, fecha_baja FROM reservas WHERE estado = 'Cancelada' AND numero_cliente = :numero_cliente");

            $consulta->bindValue(':numero_cliente', $numero_cliente, PDO::PARAM_INT);
            $consulta->execute();

            $respuesta = $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
        } else if (Usuario::obtenerCliente($numero_cliente) !== false && $incluirCancelaciones) {
            $consulta = $objAccesoDato->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta, fecha_baja FROM reservas WHERE numero_cliente = :numero_cliente");

            $consulta->bindValue(':numero_cliente', $numero_cliente, PDO::PARAM_INT);
            $consulta->execute();

            $respuesta = $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
        } else {
            $respuesta = 'No se encontro el cliente';
        }

        return $respuesta;
    }

    static function listarEntreFechas($fechaMin, $fechaMax, $soloCancelaciones = false) {
        $respuesta = [];
        $lista = self::obtenerTodos(true);

        $fechaMin = DateTime::createFromFormat("Y-m-d", $fechaMin);
        $fechaMax = DateTime::createFromFormat("Y-m-d", $fechaMax);
        foreach ($lista as $reserva) {
            $fechaReserva = DateTime::createFromFormat("Y-m-d", $reserva->fecha_entrada);

            if (!$soloCancelaciones && $reserva->estado !== 'Cancelada')
            {
                if ($fechaReserva >= $fechaMin && $fechaReserva <= $fechaMax) {
                    array_push($respuesta, $reserva);
                }
            } else if ($soloCancelaciones && $reserva->estado === 'Cancelada') {
                if ($fechaReserva >= $fechaMin && $fechaReserva <= $fechaMax) {
                    array_push($respuesta, $reserva);
                }
            }
        }

        usort($respuesta, function ($a, $b) {
            $fechaA = DateTime::createFromFormat("Y-m-d", $a->fecha_entrada);
            $fechaB = DateTime::createFromFormat("Y-m-d", $b->fecha_entrada);
    
            return $fechaA <=> $fechaB;
        });

        return $respuesta;
    }

    public function listarPorTipoHabitacion($tipo_habitacion)
    {
        $respuesta = 'No se encontraron coincidencias';
        
        if (strlen($tipo_habitacion) > 1)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDato->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta FROM reservas WHERE estado != 'Cancelada' AND tipo_habitacion = :tipo_habitacion");

            $consulta->bindValue(':tipo_habitacion', $tipo_habitacion, PDO::PARAM_STR);
            $consulta->execute();

            $respuesta = $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
        } else {
            $respuesta = 'No se encontraron reservas';
        }

        return $respuesta;
    }

    public function listarPorTipoCliente($tipo_cliente)
    {
        $respuesta = 'No se encontraron coincidencias';
        
        if (strlen($tipo_cliente) > 1)
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDato->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta, fecha_baja FROM reservas WHERE estado = 'Cancelada' AND tipo_cliente = :tipo_cliente");

            $consulta->bindValue(':tipo_cliente', $tipo_cliente, PDO::PARAM_STR);
            $consulta->execute();

            $respuesta = $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
        } else {
            $respuesta = 'No se encontraron reservas';
        }

        return $respuesta;
    }

    public function listarPorModalidadPago($modalidad_pago)
    {
        $respuesta = 'No se encontraron coincidencias';
        
        $listaClientes = Usuario::obtenerTodos();
        $numerosDeClientesPosibles = [];

        $listaReservas = self::obtenerTodos();

        foreach ($listaClientes as $cliente) {
            if ($cliente->modalidad_pago === $modalidad_pago) {
                array_push($numerosDeClientesPosibles, $cliente->numero_cliente);
            }
        }

        if (count($numerosDeClientesPosibles) > 0)
        {
            $respuesta = [];
            foreach ($listaReservas as $reserva) {
                foreach ($numerosDeClientesPosibles as $numeroCliente) {
                    if ($numeroCliente == $reserva->numero_cliente)
                    {
                        array_push($respuesta, $reserva);
                    }
                }
            }
        }

        return $respuesta;
    }

    static function listarPorTipoYFechaCanceladas($tipo_cliente, $lista, $fecha = null) {
        $respuesta = "No se pudo realizar la consulta";

        $lista = self::obtenerSoloCanceladas();

        if ($fecha !== null) {
            $fecha = DateTime::createFromFormat("Y-m-d", $fecha);
        } else {
            $fecha = new DateTime("yesterday");
        }

        $listaFiltrada = [];

        foreach ($lista as $reserva) {
            $fechaReservaE = DateTime::createFromFormat("Y-m-d", $reserva->fecha_entrada);
            $fechaReservaS = DateTime::createFromFormat("Y-m-d", $reserva->fecha_salida);

            if ($reserva->tipo_cliente == $tipo_cliente && 
            $fechaReservaE <= $fecha && $fechaReservaS >= $fecha) {
                array_push($listaFiltrada, $reserva);
            }
        }
        
        $respuesta = array_reduce($listaFiltrada, fn($acum, $r) => $acum + $r->importe, 0);

        return $respuesta;
    }

    private static function obtenerSoloCanceladas()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, tipo_cliente, numero_cliente, tipo_habitacion, fecha_entrada, fecha_salida, importe, imagen, estado, fecha_alta, fecha_baja FROM reservas WHERE estado = 'Cancelada'");
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Reserva');
    }
}