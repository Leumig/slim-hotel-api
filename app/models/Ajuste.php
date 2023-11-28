<?php

class Ajuste
{
    public $id;
    public $id_reserva;
    public $motivo;
    public $monto;
    public $fecha_alta;

    public function crearAjuste()
    {
        $retorno = 'Error al obtener el ultimo ID insertado';

        try {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ajustes (id_reserva, motivo, monto, fecha_alta) VALUES (:id_reserva, :motivo, :monto, NOW())");

            $consulta->bindValue(':id_reserva', $this->id_reserva, PDO::PARAM_INT);
            $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
            $consulta->bindValue(':monto', $this->monto);
            
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

        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_reserva, motivo, monto, fecha_alta FROM ajustes");
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Ajuste');
    }

    public static function obtenerAjuste($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_reserva, motivo, monto, fecha_alta FROM ajustes WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Ajuste');
    }
}