<?php

require_once __DIR__ . '/Conexion.php';

class UsuarioDatos
{

    public function listarUsuarios($buscar = '')
    {
        $conexion = new Conexion();
        if (!empty($buscar)) {
            // Se incluye un INNER JOIN para traer el nombre del rol directamente
            $conexion->query = "SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, 
                                       u.correo_usuario, u.estado_usuario, u.eliminado_usuario
                                FROM usuarios u
                                INNER JOIN roles r ON u.id_rol = r.id_rol
                                WHERE u.eliminado_usuario = 0 
                                AND (u.nombre_usuario LIKE :buscar 
                                     OR r.nombre_rol LIKE :buscar 
                                     OR IF(u.estado_usuario = 1, 'activo', 'inactivo') LIKE :buscar)
                                ORDER BY u.nombre_usuario ASC";
            return $conexion->get_records([':buscar' => '%' . $buscar . '%']);
        } else {
            $conexion->query = "SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, 
                                       u.correo_usuario, u.estado_usuario, u.eliminado_usuario
                                FROM usuarios u
                                INNER JOIN roles r ON u.id_rol = r.id_rol
                                WHERE u.eliminado_usuario = 0 
                                ORDER BY u.nombre_usuario ASC";
            return $conexion->get_records();
        }
    }

    private function valorNulo($valor)
    {
        return trim($valor) == '' ? null : trim($valor);
    }

    public function verificarTablaVacia()
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT COUNT(*) as total FROM usuarios";
        $resultado = $conexion->get_record();
        return (int) $resultado['total'] === 0;
    }

    public function insertarUsuario($usuario)
    {
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO usuarios (id_rol, nombre_usuario, correo_usuario, password_usuario, estado_usuario, eliminado_usuario)
                            VALUES (:idRol, :nombreUsuario, :correoUsuario, :passwordUsuario, :estadoUsuario, 0)";

        return $conexion->execute_query([
            ':idRol'           => $usuario['id_rol'],
            ':nombreUsuario'   => $this->valorNulo($usuario['nombre_usuario']),
            ':correoUsuario'   => $this->valorNulo($usuario['correo_usuario']),
            ':passwordUsuario' => $this->valorNulo($usuario['password_usuario']),
            ':estadoUsuario'   => $usuario['estado_usuario']
        ]);
    }

    public function actualizarUsuario($usuario)
    {
        $conexion = new Conexion();

        $conexion->query = "UPDATE usuarios 
                            SET id_rol = :idRol, 
                                nombre_usuario = :nombreUsuario, 
                                correo_usuario = :correoUsuario, 
                                password_usuario = :passwordUsuario,
                                estado_usuario = :estadoUsuario
                            WHERE id_usuario = :idUsuario";

        return $conexion->execute_query([
            ':idRol'           => $usuario['id_rol'],
            ':nombreUsuario'   => $this->valorNulo($usuario['nombre_usuario']),
            ':correoUsuario'   => $this->valorNulo($usuario['correo_usuario']),
            ':passwordUsuario' => $this->valorNulo($usuario['password_usuario']),
            ':estadoUsuario'   => $usuario['estado_usuario'],
            ':idUsuario'       => $usuario['id_usuario']
        ]);
    }

    public function obtenerUsuarioPorId($idUsuario)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT u.id_usuario, u.id_rol, r.nombre_rol, r.nombre_rol, u.nombre_usuario, 
                                   u.correo_usuario, u.password_usuario, u.estado_usuario, u.created_at
                            FROM usuarios u
                            INNER JOIN roles r ON u.id_rol = r.id_rol
                            WHERE u.id_usuario = :idUsuario
                            AND u.eliminado_usuario = 0";
                            
        return $conexion->get_record([
            ':idUsuario' => $idUsuario
        ]);
    }

    public function obtenerUsuarioPorCorreo($correo)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, 
                                   u.correo_usuario, u.password_usuario, u.estado_usuario, u.eliminado_usuario
                            FROM usuarios u
                            INNER JOIN roles r ON u.id_rol = r.id_rol
                            WHERE u.correo_usuario = :correoUsuario
                            AND u.eliminado_usuario = 0";
                            
        return $conexion->get_record([
            ':correoUsuario' => $correo
        ]);
    }

    public function obtenerUsuarioPorCorreoCompleto($correo)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT u.id_usuario, u.id_rol, r.nombre_rol, u.nombre_usuario, 
                                   u.correo_usuario, u.password_usuario, u.estado_usuario, u.eliminado_usuario
                            FROM usuarios u
                            INNER JOIN roles r ON u.id_rol = r.id_rol
                            WHERE u.correo_usuario = :correoUsuario";
                            
        return $conexion->get_record([
            ':correoUsuario' => $correo
        ]);
    }

    public function eliminarUsuario($idUsuario)
    {
        $conexion = new Conexion();
        // Liberar el correo para que pueda ser reutilizado por otro usuario, concatenando _del_ y el ID
        $conexion->query = "UPDATE usuarios 
                            SET eliminado_usuario = 1,
                                correo_usuario = CONCAT(SUBSTRING(correo_usuario, 1, 80), '_del_', id_usuario)
                            WHERE id_usuario = :idUsuario";

        return $conexion->execute_query([
            ':idUsuario' => $idUsuario
        ]);
    }
}