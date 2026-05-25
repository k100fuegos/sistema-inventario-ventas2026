<?php
require_once dirname(__DIR__) . '/config/Conexion.php';

class UsuarioDatos {
    
    /**
     * Busca un usuario en la base de datos por su correo electrónico
     * @param string $correo
     * @return object|false Retorna el objeto con los datos del usuario o false si no existe
     */
    public function obtenerPorCorreo($correo) {
        try {
            $db = Conexion::conectar();
            
            // Consulta que une usuarios con su rol para obtener las restricciones de acceso
            $sql = "SELECT u.*, r.nombre_rol 
                    FROM usuarios u 
                    INNER JOIN roles r ON u.id_rol = r.id_rol 
                    WHERE u.correo = :correo";
            
            $stmt = $db->prepare($sql);
            
            // Vinculamos el parámetro de forma segura para evitar Inyección SQL
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->execute();
            
            // Retorna el registro como un objeto limpio gracias a PDO::FETCH_OBJ
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            // Registra el error en el log del servidor para auditorías hambientales de código
            error_log("Error en UsuarioDatos::obtenerPorCorreo -> " . $e->getMessage());
            return false;
        }
    }
}