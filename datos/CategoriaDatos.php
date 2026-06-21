<?php

require_once __DIR__ . '/Conexion.php';

class CategoriaDatos
{



    public function listarCategorias($buscar = '')
    {
        $conexion = new Conexion();
        if (!empty($buscar)) {
            $conexion->query = "SELECT id_categoria, nombre_categoria, descripcion_categoria, estado_categoria
                            FROM categorias
                            WHERE eliminado_categoria = 0
                            AND (nombre_categoria LIKE :buscar OR IF(estado_categoria = 1, 'activo', 'inactivo') LIKE :buscar)
                            ORDER BY id_categoria DESC";
            return $conexion->get_records([':buscar' => '%' . $buscar . '%']);
        } else {
            $conexion->query = 'SELECT id_categoria, nombre_categoria, descripcion_categoria, estado_categoria
                            FROM categorias
                            WHERE eliminado_categoria = 0
                            ORDER BY id_categoria DESC';
            return $conexion->get_records();
        }
    }

    public function insertarCategoria($categoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'INSERT INTO categorias (nombre_categoria, descripcion_categoria, estado_categoria, eliminado_categoria)
                         VALUES (:nombreCategoria, :descripcionCategoria, :estadoCategoria, 0)';

        return $conexion->execute_query([
            ':nombreCategoria' => $categoria['nombre_categoria'],
            ':descripcionCategoria' => $categoria['descripcion_categoria'],
            ':estadoCategoria' => $categoria['estado_categoria']
        ]);
    }

    public function actualizarCategoria($categoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE categorias
                         SET nombre_categoria = :nombreCategoria,
                             descripcion_categoria = :descripcionCategoria,
                             estado_categoria = :estadoCategoria
                         WHERE id_categoria = :idCategoria
                         AND eliminado_categoria = 0';

        return $conexion->execute_query([
            ':nombreCategoria' => $categoria['nombre_categoria'],
            ':descripcionCategoria' => $categoria['descripcion_categoria'],
            ':estadoCategoria' => $categoria['estado_categoria'],
            ':idCategoria' => $categoria['id_categoria']
        ]);
    }

    public function obtenerCategoriaPorId($idCategoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'SELECT id_categoria, nombre_categoria, descripcion_categoria, estado_categoria
                        FROM categorias
                        WHERE id_categoria = :idCategoria
                        AND eliminado_categoria = 0
                        LIMIT 1';

        return $conexion->get_record([
            ':idCategoria' => $idCategoria
        ]);
    }

    public function obtenerCategoriaPorNombre($nombreCategoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'SELECT id_categoria,
                               nombre_categoria,
                               descripcion_categoria,
                               estado_categoria,
                               eliminado_categoria
                        FROM categorias
                        WHERE nombre_categoria = :nombreCategoria
                        LIMIT 1';

        return $conexion->get_record([
            ':nombreCategoria' => $nombreCategoria
        ]);
    }

    public function reactivarCategoria($categoria)
    {
        $conexion = new Conexion();

        $conexion->query = 'UPDATE categorias
                        SET nombre_categoria = :nombreCategoria,
                            descripcion_categoria = :descripcionCategoria,
                            estado_categoria = :estadoCategoria,
                            eliminado_categoria = 0
                        WHERE id_categoria = :idCategoria';

        return $conexion->execute_query([
            ':nombreCategoria'      => $categoria['nombre_categoria'],
            ':descripcionCategoria' => $categoria['descripcion_categoria'],
            ':estadoCategoria'      => $categoria['estado_categoria'],
            ':idCategoria'          => $categoria['id_categoria']
        ]);
    }

    public function eliminarCategoria($idCategoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE categorias
                         SET eliminado_categoria = 1
                         WHERE id_categoria = :idCategoria';

        return $conexion->execute_query([
            ':idCategoria' => $idCategoria
        ]);
    }
}
