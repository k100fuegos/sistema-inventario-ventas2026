<?php

require_once __DIR__ . '/Conexion.php';

class CategoriaDatos
{



    public function listarCategorias()
    {

        $conexion = new Conexion();
        $conexion->query = 'SELECT id_categoria, nombre_categoria, descripcion_categoria
                        FROM categorias
                        WHERE estado_categoria = 1
                        ORDER BY id_categoria DESC';

        return $conexion->get_records();
    }

    public function insertarCategoria($categoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'INSERT INTO categorias (nombre_categoria, descripcion_categoria,estado_categoria)
                         VALUES (:nombreCategoria, :descripcionCategoria, 1)';

        return $conexion->execute_query([
            ':nombreCategoria' => $categoria['nombre_categoria'],
            ':descripcionCategoria' => $categoria['descripcion_categoria']
        ]);
    }

    public function actualizarCategoria($categoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE categorias
                         SET nombre_categoria = :nombreCategoria,
                             descripcion_categoria = :descripcionCategoria
                         WHERE id_categoria = :idCategoria';

        return $conexion->execute_query([
            ':nombreCategoria' => $categoria['nombre_categoria'],
            ':descripcionCategoria' => $categoria['descripcion_categoria'],
            ':idCategoria' => $categoria['id_categoria']
        ]);
    }

    public function obtenerCategoriaPorId($idCategoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'SELECT id_categoria, nombre_categoria, descripcion_categoria
                        FROM categorias
                        WHERE id_categoria = :idCategoria';

        return $conexion->get_record([
            ':idCategoria' => $idCategoria
        ]);
    }

    public function eliminarCategoria($idCategoria)
    {
        $conexion = new Conexion();
        $conexion->query = 'UPDATE categorias
                         SET estado_categoria = 0
                         WHERE id_categoria = :idCategoria';

        return $conexion->execute_query([
            ':idCategoria' => $idCategoria
        ]);
    }
}
