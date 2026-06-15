<?php

require_once __DIR__ . '/Conexion.php';

class MarcaDatos
{



    public function listarMarcas()
    {

        $conexion = new Conexion();
        $conexion->query = 'SELECT id_marca, nombre_marca
                        FROM marcas
                        WHERE estado_marca = 1
                        ORDER BY id_marca DESC';

        return $conexion->get_records();
    }

    public function insertarMarca($marca)
    {
        $conexion = new Conexion();
        $conexion->query = 'INSERT INTO marcas (nombre_marca, estado_marca)
                         VALUES (:nombreMarca, 1)';

        return $conexion->execute_query([
            ':nombreMarca' => $marca['nombre_marca'],
        ]);
    }

    public function actualizarMarca($marca)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE marcas
                         SET nombre_marca = :nombreMarca
                         WHERE id_marca = :idMarca';

        return $conexion->execute_query([
            ':nombreMarca' => $marca['nombre_marca'],
            ':idMarca' => $marca['id_marca']
        ]);
    }

    public function obtenerMarcaPorId($idMarca)
    {
        $conexion = new Conexion();
        $conexion->query = 'SELECT id_marca, nombre_marca
                        FROM marcas
                        WHERE id_marca = :idMarca';

        return $conexion->get_record([
            ':idMarca' => $idMarca
        ]);
    }

    public function eliminarMarca($idMarca)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE marca
                         SET estado_marca = 0
                         WHERE id_marca = :idMarca';

        return $conexion->execute_query([
            ':idMarca' => $idMarca
        ]);
    }
}
