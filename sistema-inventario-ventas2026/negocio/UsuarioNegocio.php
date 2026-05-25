<?php
require_once dirname(__DIR__) . '/datos/UsuarioDatos.php';

class UsuarioNegocio {
    private $usuarioDatos;

    public function __construct() {
        // Instanciamos la capa de acceso a datos
        $this->usuarioDatos = new UsuarioDatos();
    }

    /**
     * Procesa y valida las reglas de negocio para el inicio de sesión
     * @param string $correo
     * @param string $password
     * @return array Respuesta estructurada con el estado del proceso y mensajes informativos
     */
    public function iniciarSesion($correo, $password) {
        //Limpieza básica de espacios en blanco
        $correo = trim($correo);
        
        //Solicitar los datos a la capa de acceso a datos
        $usuario = $this->usuarioDatos->obtenerPorCorreo($correo);
        
        //Regla de Negocio 1: Validar existencia del usuario
        if (!$usuario) {
            return [
                'status' => false,
                'mensaje' => 'El correo electrónico ingresado no coincide con ninguna cuenta.'
            ];
        }
        
        //Regla de Negocio 2: Validar si el empleado está activo en la tienda
        if ($usuario->estado != 1) {
            return [
                'status' => false,
                'mensaje' => 'Esta cuenta se encuentra temporalmente inactiva. Contacte al administrador.'
            ];
        }
        
        //Regla de Negocio 3: Verificar la contraseña usando password_verify
        //Compara el texto plano del formulario con el hash de 255 caracteres de la BD
        if (!password_verify($password, $usuario->password)) {
            return [
                'status' => false,
                'mensaje' => 'La contraseña ingresada es incorrecta.'
            ];
        }
        
        //Si pasa todos los filtros, el acceso es exitoso y retornamos el perfil
        return [
            'status' => true,
            'mensaje' => 'Acceso concedido correctamente a Tecnobyte.',
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'id_rol'     => $usuario->id_rol,
                'nombre'     => $usuario->nombre,
                'nombre_rol' => $usuario->nombre_rol,
                'dui'        => $usuario->dui,
                'correo'     => $usuario->correo
            ]
        ];
    }
}