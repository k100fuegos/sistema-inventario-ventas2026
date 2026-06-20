<?php

require_once __DIR__ . '/Conexion.php';

class MarcaDatos
{
    public function listarMarcas()
    {
        $conexion = new Conexion();
        $conexion->query = 'SELECT id_marca, nombre_marca, estado_marca
                        FROM marcas
                        WHERE eliminado_marca = 0
                        ORDER BY id_marca DESC';

        return $conexion->get_records();
    }

    public function insertarMarca($marca)
    {
        $conexion = new Conexion();
        $conexion->query = 'INSERT INTO marcas (nombre_marca, estado_marca, eliminado_marca)
                         VALUES (:nombreMarca, :estadoMarca, 0)';

        return $conexion->execute_query([
            ':nombreMarca' => $marca['nombre_marca'],
            ':estadoMarca' => $marca['estado_marca']
        ]);
    }

    public function actualizarMarca($marca)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE marcas
                         SET nombre_marca = :nombreMarca,
                             estado_marca = :estadoMarca
                         WHERE id_marca = :idMarca
                         AND eliminado_marca = 0';

        return $conexion->execute_query([
            ':nombreMarca' => $marca['nombre_marca'],
            ':estadoMarca' => $marca['estado_marca'],
            ':idMarca'     => $marca['id_marca']
        ]);
    }

    public function obtenerMarcaPorId($idMarca)
    {
        $conexion = new Conexion();
        $conexion->query = 'SELECT id_marca, nombre_marca, estado_marca
                        FROM marcas
                        WHERE id_marca = :idMarca
                        AND eliminado_marca = 0
                        LIMIT 1';

        return $conexion->get_record([
            ':idMarca' => $idMarca
        ]);
    }

    public function obtenerMarcaPorNombre($nombreMarca)
    {
        $conexion = new Conexion();
        $conexion->query = 'SELECT id_marca, 
                               nombre_marca, 
                               estado_marca, 
                               eliminado_marca
                        FROM marcas
                        WHERE nombre_marca = :nombreMarca
                        LIMIT 1';

        return $conexion->get_record([
            ':nombreMarca' => $nombreMarca
        ]);
    }

    public function reactivarMarca($marca)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE marcas
                        SET nombre_marca = :nombreMarca,
                            estado_marca = :estadoMarca,
                            eliminado_marca = 0
                        WHERE id_marca = :idMarca';

        return $conexion->execute_query([
            ':nombreMarca' => $marca['nombre_marca'],
            ':estadoMarca' => $marca['estado_marca'],
            ':idMarca'     => $marca['id_marca']
        ]);
    }

    public function eliminarMarca($idMarca)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE marcas
                         SET eliminado_marca = 1
                         WHERE id_marca = :idMarca';

        return $conexion->execute_query([
            ':idMarca' => $idMarca
        ]);
    }
}