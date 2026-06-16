<?php

require_once dirname(__DIR__) . '/datos/UsuarioDatos.php';

class UsuarioNegocio
{

    private $usuarioDatos;

    public function __construct()
    {
        $this->usuarioDatos = new UsuarioDatos();
    }

    public function listarUsuarios()
    {
        return $this->usuarioDatos->listarUsuarios();
    }

    private function limpiarDatos($datos)
    {
        return [
            'id_usuario'       => isset($datos['id_usuario']) ? (int) $datos['id_usuario'] : null,
            'id_rol'           => isset($datos['id_rol']) ? (int) $datos['id_rol'] : null,
            'nombre_usuario'   => isset($datos['nombre_usuario']) ? trim($datos['nombre_usuario']) : '',
            'correo_usuario'   => isset($datos['correo_usuario']) ? trim($datos['correo_usuario']) : '',
            'password_usuario' => isset($datos['password_usuario']) ? trim($datos['password_usuario']) : '',
            'estado_usuario'   => isset($datos['estado_usuario']) ? (int) $datos['estado_usuario'] : 1,
            'eliminado_usuario' => isset($datos['eliminado_usuario']) ? (int)$datos['eliminado_usuario'] : 0,
        ];
    }

    private function validarUsuario($datos, $esActualizacion = false)
    {
        $errores = [];

        // Validación del Rol
        if (!isset($datos['id_rol']) || empty($datos['id_rol']) || (int)$datos['id_rol'] <= 0) {
            $errores[] = "Debe asignar un rol válido al usuario";
        }

        // Validación del Nombre
        if (!isset($datos['nombre_usuario']) || empty(trim($datos['nombre_usuario']))) {
            $errores[] = "El nombre del usuario es obligatorio";
        } elseif (strlen(trim($datos['nombre_usuario'])) > 100) {
            $errores[] = "El nombre del usuario no debe superar los 100 caracteres";
        }

        // Validación del Correo electrónico
        if (!isset($datos['correo_usuario']) || empty(trim($datos['correo_usuario']))) {
            $errores[] = "El correo electrónico es obligatorio";
        } elseif (strlen(trim($datos['correo_usuario'])) > 100) {
            $errores[] = "El correo electrónico no debe superar los 100 caracteres";
        } elseif (!filter_var(trim($datos['correo_usuario']), FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido";
        }

        // Validación de la Contraseña
        if (!$esActualizacion) {
            // Si es un registro nuevo, el password es obligatorio
            if (!isset($datos['password_usuario']) || empty(trim($datos['password_usuario']))) {
                $errores[] = "La contraseña es obligatoria para nuevos usuarios";
            }
        } else {
            // Si se está actualizando, podrías permitir que venga vacío si no se desea cambiar
            // Esta regla se puede ajustar según el flujo que decidas en tu frontend
            if (isset($datos['password_usuario']) && !empty(trim($datos['password_usuario'])) && strlen(trim($datos['password_usuario'])) > 255) {
                $errores[] = "La contraseña no debe superar los 255 caracteres";
            }

            // cantidad minima de caracteres
            if (isset($datos['password_usuario']) && !empty(trim($datos['password_usuario'])) && strlen(trim($datos['password_usuario'])) < 8) {
                $errores[] = "La contraseña debe tener al menos 8 caracteres";
            }
        }

        // Validación del Estado
        if (isset($datos['estado_usuario']) && !in_array((int)$datos['estado_usuario'], [0, 1], true)) {
            $errores[] = "El estado del usuario no es válido";
        }


        return $errores;
    }

    public function crearUsuario($datos)
    {
        $errores = $this->validarUsuario($datos, false);

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $usuario = $this->limpiarDatos($datos);

        // RECOMENDACIÓN: Encriptar la contraseña antes de mandarla a la capa de datos
        $usuario['password_usuario'] = password_hash($usuario['password_usuario'], PASSWORD_BCRYPT);

        $resultado = $this->usuarioDatos->insertarUsuario($usuario);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Usuario registrado correctamente' : 'No se pudo registrar el usuario'
        ];
    }

    public function obtenerUsuarioPorId($idUsuario)
    {
        if (!is_numeric($idUsuario) || $idUsuario <= 0) {
            return null;
        }

        return $this->usuarioDatos->obtenerUsuarioPorId($idUsuario);
    }

    public function actualizarUsuario($datos)
    {
        $errores = $this->validarUsuario($datos, true);

        if (!isset($datos['id_usuario']) || empty($datos['id_usuario'])) {
            $errores[] = "El identificador del usuario es obligatorio";
        }

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $usuario = $this->limpiarDatos($datos);
        $usuario['id_usuario'] = (int) $datos['id_usuario'];

        // LÓGICA DE CONTRASEÑA EN ACTUALIZACIÓN:
        // Si el usuario no envió una nueva contraseña, recuperamos la actual para no perderla
        if (empty(trim($datos['password_usuario'] ?? ''))) {
            $usuarioExistente = $this->usuarioDatos->obtenerUsuarioPorId($usuario['id_usuario']);
            // Nota: Para este caso específico, tu método 'obtenerUsuarioPorId' debe retornar el password,
            // de lo contrario, deberás ajustar tu query de datos o manejarlo desde el controlador.
            $usuario['password_usuario'] = $usuarioExistente['password_usuario'] ?? '';
        } else {
            // Si envió una nueva, la encriptamos
            $usuario['password_usuario'] = password_hash($usuario['password_usuario'], PASSWORD_BCRYPT);
        }

        $resultado = $this->usuarioDatos->actualizarUsuario($usuario);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Usuario actualizado correctamente' : 'No se pudo actualizar el usuario'
        ];
    }

    public function eliminarUsuario($idUsuario)
    {
        if (!is_numeric($idUsuario) || $idUsuario <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'El identificador del usuario no es válido'
            ];
        }

        $resultado = $this->usuarioDatos->eliminarUsuario($idUsuario);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Usuario eliminado correctamente' : 'No se pudo eliminar el usuario'
        ];
    }
}
