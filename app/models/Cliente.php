<?php

class Cliente
{
    // public $id;
    public $nombre;
    public $apellido;
    public $tipo_documento;
    public $numero_documento;
    public $email;
    public $tipo_cliente;
    public $pais;
    public $ciudad;
    public $telefono;
    public $modalidad_pago;
    public $imagen;
    public $numero_cliente;
    public $estado;
    public $fecha_alta;
    public $fecha_baja;

    public function crearCliente($fechasPreestablecidas = false)
    {
        $retorno = 'Error al obtener el ultimo ID insertado';

        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            if (!$fechasPreestablecidas)
            {
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO clientes (nombre, apellido, tipo_documento, numero_documento, email, tipo_cliente, pais, ciudad, telefono, modalidad_pago, estado, fecha_alta) VALUES (:nombre, :apellido, :tipo_documento, :numero_documento, :email, :tipo_cliente, :pais, :ciudad, :telefono, :modalidad_pago, :estado, NOW())");

                //$imagen = self::guardarImagen();

                $estado = 'Activo';
                // $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
                // $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
                $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
                $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
                $consulta->bindValue(':tipo_documento', $this->tipo_documento, PDO::PARAM_STR);
                $consulta->bindValue(':numero_documento', $this->numero_documento, PDO::PARAM_INT);
                $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
                $consulta->bindValue(':tipo_cliente', $this->tipo_cliente, PDO::PARAM_STR);
                $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
                $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
                $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_INT);
                $consulta->bindValue(':modalidad_pago', $this->modalidad_pago, PDO::PARAM_STR);
                // $consulta->bindValue(':imagen', $imagen, PDO::PARAM_STR);
                $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            } else {
                $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO clientes (nombre, apellido, tipo_documento, numero_documento, email, tipo_cliente, pais, ciudad, telefono, modalidad_pago, imagen, numero_cliente, estado, fecha_alta, fecha_baja) VALUES (:nombre, :apellido, :tipo_documento, :numero_documento, :email, :tipo_cliente, :pais, :ciudad, :telefono, :modalidad_pago, :imagen, :numero_cliente, :estado, :fecha_alta, :fecha_baja)");

                // $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
                // $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
                $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
                $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
                $consulta->bindValue(':tipo_documento', $this->tipo_documento, PDO::PARAM_STR);
                $consulta->bindValue(':numero_documento', $this->numero_documento, PDO::PARAM_INT);
                $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
                $consulta->bindValue(':tipo_cliente', $this->tipo_cliente, PDO::PARAM_STR);
                $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
                $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
                $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_INT);
                $consulta->bindValue(':modalidad_pago', $this->modalidad_pago, PDO::PARAM_STR);
                $consulta->bindValue(':imagen', $this->imagen, PDO::PARAM_STR);
                $consulta->bindValue(':numero_cliente', $this->numero_cliente, PDO::PARAM_INT);
                $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
                $consulta->bindValue(':fecha_alta', $this->fecha_alta);
                $consulta->bindValue(':fecha_baja', $this->fecha_baja);
            }

            $consulta->execute();
            $retorno = $objAccesoDatos->obtenerUltimoId();
        } catch (PDOException $e) {
            $retorno = 'Error al ejecutar la consulta: ' . $e->getMessage();
        }

        return $retorno;
    }

    public function guardarImagen($numero_cliente) {
        if (isset($_FILES['imagen']))
        {
            $nombreImagen = $numero_cliente . $this->tipo_cliente . '.jpg';
            $this->imagen = $nombreImagen;

            $destino = 'img/ImagenesDeClientes/2023/' . $nombreImagen;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $destino);

            if (strlen($nombreImagen) > 4)
            {
                $objAccesoDatos = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDatos->prepararConsulta("UPDATE clientes SET imagen = :imagen WHERE numero_cliente = :numero_cliente");

                $consulta->bindValue(':imagen', $nombreImagen);
                $consulta->bindValue(':numero_cliente', $numero_cliente);

                $consulta->execute();
            }
        } else {
            $nombreImagen = 'N/A';
        }

        return $nombreImagen;
    }

    public static function validarClienteExiste($numero_cliente, $numero_documento)
    {
        $existe = false;
        $clientes = Cliente::obtenerTodos();

        foreach ($clientes as $cliente) {
            if ($cliente->numero_cliente == $numero_cliente && $cliente->numero_documento == $numero_documento) {
                $existe = true;
            }
        }

        return $existe;
    }

    public static function obtenerTodos($incluirEliminados = false)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        if (!$incluirEliminados)
        {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre, apellido, tipo_documento, numero_documento, email, tipo_cliente, pais, ciudad, telefono, modalidad_pago, imagen, numero_cliente, estado, fecha_alta FROM clientes WHERE estado != 'Eliminado'");
        } else {
            $consulta = $objAccesoDatos->prepararConsulta("SELECT numero_cliente, nombre, apellido, tipo_documento, numero_documento, email, tipo_cliente, pais, ciudad, telefono, modalidad_pago, imagen, numero_cliente, estado, fecha_alta FROM clientes");
        }
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cliente');
    }

    public static function obtenerCliente($numero_cliente)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre, apellido, tipo_documento, numero_documento, email, tipo_cliente, pais, ciudad, telefono, modalidad_pago, imagen, numero_cliente, estado, fecha_alta FROM clientes WHERE numero_cliente = :numero_cliente AND estado != 'Eliminado'");
        $consulta->bindValue(':numero_cliente', $numero_cliente, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Cliente');
    }

    public static function modificarCliente($numero_cliente, $nombre, $apellido, $email, $tipo_cliente, $tipo_documento, $numero_documento, $pais, $ciudad, $telefono, $modalidad_pago)
    {
        $retorno = 'Error al modificar Cliente';

        try {
            if (self::validarClienteExiste($numero_cliente, $numero_documento))
            {
                $objAccesoDato = AccesoDatos::obtenerInstancia();
                $consulta = $objAccesoDato->prepararConsulta("UPDATE clientes SET nombre = :nombre, apellido = :apellido, email = :email, tipo_cliente = :tipo_cliente, tipo_documento = :tipo_documento, numero_documento = :numero_documento, pais = :pais, ciudad = :ciudad, telefono = :telefono, modalidad_pago = :modalidad_pago WHERE numero_cliente = :numero_cliente");
                
                $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
                $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
                $consulta->bindValue(':tipo_documento', $tipo_documento, PDO::PARAM_STR);
                $consulta->bindValue(':numero_documento', $numero_documento, PDO::PARAM_INT);
                $consulta->bindValue(':email', $email, PDO::PARAM_STR);
                $consulta->bindValue(':tipo_cliente', $tipo_cliente, PDO::PARAM_STR);
                $consulta->bindValue(':pais', $pais, PDO::PARAM_STR);
                $consulta->bindValue(':ciudad', $ciudad, PDO::PARAM_STR);
                $consulta->bindValue(':telefono', $telefono, PDO::PARAM_INT);
                $consulta->bindValue(':modalidad_pago', $modalidad_pago, PDO::PARAM_STR);
                $consulta->bindValue(':numero_cliente', $numero_cliente, PDO::PARAM_INT);
                $consulta->execute();

                $retorno = 'Cliente modificado con exito, Numero: ' . $numero_cliente;
            } else {
                $retorno = 'No se encontro al cliente';
            }
        } catch (PDOException $e) {
            $retorno = 'Error al ejecutar la consulta: ' . $e->getMessage();
        }

        return $retorno;
    }

    public static function borrarCliente($cliente)
    {
        $retorno = 'Error al eliminar Cliente';

        try {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta("UPDATE clientes SET estado = :estado, fecha_baja = NOW() WHERE numero_cliente = :numero_cliente AND estado != 'Eliminado'");

            $estado = 'Eliminado';
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
            $consulta->bindValue(':numero_cliente', $cliente->numero_cliente, PDO::PARAM_INT);
            $consulta->execute();
            $retorno = 'Cliente eliminado con exito, Numero: ' . $cliente->numero_cliente;

            $ubicacionActual = "img/ImagenesDeClientes/2023/" . $cliente->imagen;
            $destino = "img/ImagenesBackupClientes/2023/" . $cliente->imagen;
            rename($ubicacionActual, $destino);
        } catch (PDOException $e) {
            $retorno = 'Error al ejecutar la consulta: ' . $e->getMessage();
        }

        return $retorno;
    }

    public static function obtenerPorNumeros($numero_cliente, $numero_documento)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pais, ciudad, telefono FROM clientes WHERE numero_cliente = :numero_cliente AND numero_documento = :numero_documento AND estado != 'Eliminado'");

        $consulta->bindValue(':numero_documento', $numero_documento, PDO::PARAM_INT);
        $consulta->bindValue(':numero_cliente', $numero_cliente, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    /*
    public static function crearPorCampos($datos)
    {
        $id = isset($datos[0]) ? $datos[0] : null;
        $usuario = isset($datos[1]) ? $datos[1] : null;
        $clave = isset($datos[2]) ? $datos[2] : null;
        $rol = isset($datos[3]) ? $datos[3] : null;
        $email = isset($datos[4]) ? $datos[4] : null;
        $nombre = isset($datos[5]) ? $datos[5] : null;
        $apellido = isset($datos[6]) ? $datos[6] : null;
        $estado = isset($datos[7]) ? $datos[7] : null;
        $fecha_alta = isset($datos[8]) ? $datos[8] : null;
        $fecha_baja = isset($datos[9]) ? $datos[9] : null;
    
        if ($clave !== null)
        {
            $usuarioNuevo = new Cliente();
            $usuarioNuevo->id = $id;
            $usuarioNuevo->usuario = $usuario;
            $usuarioNuevo->clave = $clave;
            $usuarioNuevo->rol = $rol;
            $usuarioNuevo->email = $email;
            $usuarioNuevo->nombre = $nombre;
            $usuarioNuevo->apellido = $apellido;
            $usuarioNuevo->estado = $estado;
            $usuarioNuevo->fecha_alta = ($fecha_alta) ? $fecha_alta : null;
            $usuarioNuevo->fecha_baja = ($fecha_baja) ? $fecha_baja : null;

            $usuarioNuevo->crearCliente(true);
        }
    }*/
}