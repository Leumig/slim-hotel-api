<?php

class Gerente
{
    public $usuario;
    public $clave;
    public $nombre;
    public $apellido;
    public $email;
    public $pais;
    public $ciudad;
    public $telefono;
    public $tipo_documento;
    public $numero_documento;
    public $tipo_cliente;
    public $modalidad_pago;
    public $estado;
    public $fecha_alta;
    public $fecha_baja;
    public $rol;

    public function crearGerente()
    {
        $retorno = 'Error al obtener el ultimo ID insertado';

        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, rol, nombre, apellido, tipo_documento, numero_documento, email, tipo_cliente, pais, ciudad, telefono, modalidad_pago, estado, fecha_alta) VALUES (:usuario, :clave, :rol, :nombre, :apellido, :tipo_documento, :numero_documento, :email, :tipo_cliente, :pais, :ciudad, :telefono, :modalidad_pago, :estado, NOW())");
            
            $rol = 'Gerente';
            $tipo_documento = 'N/A';
            $numero_documento = '0000000';
            $tipo_cliente = 'Ninguno';
            $modalidad_pago = 'Ninguno';
            $estado = 'Activo';

            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
            $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
            $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
            $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_INT);

            $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);
            $consulta->bindValue(':tipo_documento', $tipo_documento, PDO::PARAM_STR);
            $consulta->bindValue(':numero_documento', $numero_documento, PDO::PARAM_INT);
            $consulta->bindValue(':tipo_cliente', $tipo_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':modalidad_pago', $modalidad_pago, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);

            $consulta->execute();
            $retorno = $objAccesoDatos->obtenerUltimoId();
        } catch (PDOException $e) {
            $retorno = 'Error al ejecutar la consulta: ' . $e->getMessage();
        }

        return $retorno;
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT usuario, clave, rol, nombre, apellido, email, pais, ciudad, telefono, tipo_documento, numero_documento, tipo_cliente, modalidad_pago, estado, fecha_alta FROM usuarios WHERE rol = 'Gerente'");

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Gerente');
    }
}