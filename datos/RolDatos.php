<?php

require_once __DIR__ . '/Conexion.php';

class RolDatos
{

    public function listarRoles()
    {
        $conexion = new Conexion();
        // Solo listamos los roles activos (estado_rol = 1)
        $conexion->query = "SELECT id_rol, nombre_rol 
                            FROM roles 
                            WHERE estado_rol = 1
                            ORDER BY nombre_rol ASC";
                            
        return $conexion->get_records();
    }

    private function valorNulo($valor)
    {
        return trim($valor) == '' ? null : trim($valor);
    }

    public function insertarRol($rol)
    {
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO roles (nombre_rol, estado_rol)
                            VALUES (:nombreRol, 1)";

        return $conexion->execute_query([
            ':nombreRol' => $this->wildcard($rol['nombre_rol'])
        ]);
    }

    private function wildcard($valor) {
        return $this->valorNulo($valor);
    }

    public function actualizarRol($rol)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE roles 
                            SET nombre_rol = :nombreRol 
                            WHERE id_rol = :idRol";

        return $conexion->execute_query([
            ':nombreRol' => $this->valorNulo($rol['nombre_rol']),
            ':idRol'     => $rol['id_rol']
        ]);
    }

    public function obtenerRolPorId($idRol)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT id_rol, nombre_rol, estado_rol
                            FROM roles 
                            WHERE id_rol = :idRol AND estado_rol = 1";
                            
        return $conexion->get_record([
            ':idRol' => $idRol
        ]);
    }

    public function eliminarRol($idRol)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE roles 
                            SET estado_rol = 0 
                            WHERE id_rol = :idRol";

        return $conexion->execute_query([
            ':idRol' => $idRol
        ]);
    }
}