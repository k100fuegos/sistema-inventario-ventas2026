<?php

require_once __DIR__ . '/Conexion.php';

class UsuarioDatos
{

    public function listarUsuarios()
    {
        $conexion = new Conexion();
        // Se incluye un INNER JOIN para traer el nombre del rol directamente
        $conexion->query = "SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, 
                                   u.correo_usuario, u.estado_usuario
                            FROM usuarios u
                            INNER JOIN roles r ON u.id_rol = r.id_rol
                            WHERE u.estado_usuario = 1 
                            ORDER BY u.nombre_usuario ASC";
                            
        return $conexion->get_records();
    }

    private function valorNulo($valor)
    {
        return trim($valor) == '' ? null : trim($valor);
    }

    public function insertarUsuario($usuario)
    {
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO usuarios (id_rol, nombre_usuario, correo_usuario, password_usuario, estado_usuario)
                            VALUES (:idRol, :nombreUsuario, :correoUsuario, :passwordUsuario, 1)";

        return $conexion->execute_query([
            ':idRol'           => $usuario['id_rol'],
            ':nombreUsuario'   => $this->valorNulo($usuario['nombre_usuario']),
            ':correoUsuario'   => $this->valorNulo($usuario['correo_usuario']),
            ':passwordUsuario' => $this->valorNulo($usuario['password_usuario']),
        ]);
    }

    public function actualizarUsuario($usuario)
    {
        $conexion = new Conexion();

        $conexion->query = "UPDATE usuarios 
                            SET id_rol = :idRol, 
                                nombre_usuario = :nombreUsuario, 
                                correo_usuario = :correoUsuario, 
                                password_usuario = :passwordUsuario
                            WHERE id_usuario = :idUsuario";

        return $conexion->execute_query([
            ':idRol'           => $usuario['id_rol'],
            ':nombreUsuario'   => $this->valorNulo($usuario['nombre_usuario']),
            ':correoUsuario'   => $this->valorNulo($usuario['correo_usuario']),
            ':passwordUsuario' => $this->valorNulo($usuario['password_usuario']),
            ':idUsuario'       => $usuario['id_usuario']
        ]);
    }

    public function obtenerUsuarioPorId($idUsuario)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, 
                                   u.correo_usuario, u.password_usuario, u.estado_usuario, u.created_at
                            FROM usuarios u
                            INNER JOIN roles r ON u.id_rol = r.id_rol
                            WHERE u.id_usuario = :idUsuario
                            AND u.estado_usuario = 1";
                            
        return $conexion->get_record([
            ':idUsuario' => $idUsuario
        ]);
    }

    public function eliminarUsuario($idUsuario)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE usuarios 
                            SET estado_usuario = 0 
                            WHERE id_usuario = :idUsuario";

        return $conexion->execute_query([
            ':idUsuario' => $idUsuario
        ]);
    }
}