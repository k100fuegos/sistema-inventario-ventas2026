<?php

require_once dirname(__DIR__) . '/datos/UsuarioDatos.php';

class UsuarioNegocio
{

    private $usuarioDatos;

    public function __construct()
    {
        $this->usuarioDatos = new UsuarioDatos();
    }

    public function iniciarSesion($correo, $password)
    {
        if (empty(trim($correo)) || empty(trim($password))) {
            return ['status' => false, 'mensaje' => 'El correo y la contraseña son obligatorios'];
        }

        $usuario = $this->usuarioDatos->obtenerUsuarioPorCorreo($correo);

        if ($usuario) {
            if ($usuario['estado_usuario'] != 1) {
                return ['status' => false, 'mensaje' => 'El usuario se encuentra inactivo'];
            }

            // Validar hash o texto plano para entorno de pruebas
            if (password_verify($password, $usuario['password_usuario']) || $password === $usuario['password_usuario']) {
                $usuarioLogin = [
                    'id_usuario' => $usuario['id_usuario'],
                    'id_rol'     => $usuario['id_rol'],
                    'nombre'     => $usuario['nombre_usuario'],
                    'nombre_rol' => $usuario['nombre_rol'],
                    'dui'        => $usuario['dui'] ?? ''
                ];
                return ['status' => true, 'usuario' => $usuarioLogin];
            } else {
                return ['status' => false, 'mensaje' => 'La contraseña es incorrecta'];
            }
        } else {
            return ['status' => false, 'mensaje' => 'No se encontró un usuario con ese correo'];
        }
    }

    public function listarUsuarios($buscar = '')
    {
        return $this->usuarioDatos->listarUsuarios($buscar);
    }

    private function limpiarDatos($datos)
    {
        return [
            'id_usuario'       => isset($datos['id_usuario']) ? (int) $datos['id_usuario'] : null,
            'id_rol'           => (int) $datos['id_rol'],
            'nombre_usuario'   => isset($datos['nombre_usuario']) ? trim($datos['nombre_usuario']) : '',
            'correo_usuario'   => isset($datos['correo_usuario']) ? trim($datos['correo_usuario']) : '',
            'password_usuario' => isset($datos['password_usuario']) ? trim($datos['password_usuario']) : '',
            'confirmation_password_usuario' => isset($datos['confirmation_password_usuario']) ? trim($datos['confirmation_password_usuario']) : '',
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

            // Obligatoria al crear
            if (!isset($datos['password_usuario']) || empty(trim($datos['password_usuario']))) {
                $errores[] = "La contraseña es obligatoria para nuevos usuarios";
            }

            if (isset($datos['password_usuario']) && strlen(trim($datos['password_usuario'])) > 255) {
                $errores[] = "La contraseña no debe superar los 255 caracteres";
            }

            if (isset($datos['password_usuario']) && strlen(trim($datos['password_usuario'])) < 8) {
                $errores[] = "La contraseña debe tener al menos 8 caracteres";
            }

            // Debe contener una letra
            if (isset($datos['password_usuario']) && !preg_match('/[A-Za-z]/', $datos['password_usuario'])) {
                $errores[] = "La contraseña debe contener al menos una letra";
            }

            // Debe contener un número
            if (isset($datos['password_usuario']) && !preg_match('/[0-9]/', $datos['password_usuario'])) {
                $errores[] = "La contraseña debe contener al menos un número";
            }

            // Debe contener un carácter especial
            if (isset($datos['password_usuario']) && !preg_match('/[\W_]/', $datos['password_usuario'])) {
                $errores[] = "La contraseña debe contener al menos un carácter especial";
            }

            // Confirmación obligatoria
            if (!isset($datos['confirmation_password_usuario']) || empty(trim($datos['confirmation_password_usuario']))) {
                $errores[] = "La confirmación de la contraseña es obligatoria";
            }

            // Deben coincidir
            if (
                isset($datos['password_usuario']) &&
                isset($datos['confirmation_password_usuario']) &&
                $datos['password_usuario'] !== $datos['confirmation_password_usuario']
            ) {
                $errores[] = "Las contraseñas no coinciden";
            }
        } else {

            // En edición solo validar si alguno de los dos campos fue llenado
            $password = trim($datos['password_usuario'] ?? '');
            $confirmacion = trim($datos['confirmation_password_usuario'] ?? '');

            if ($password !== '' || $confirmacion !== '') {

                // Ambos deben estar llenos
                if ($password === '') {
                    $errores[] = "Debe ingresar la nueva contraseña";
                }

                if ($confirmacion === '') {
                    $errores[] = "Debe confirmar la nueva contraseña";
                }

                // Longitud máxima
                if ($password !== '' && strlen($password) > 255) {
                    $errores[] = "La contraseña no debe superar los 255 caracteres";
                }

                // Longitud mínima
                if ($password !== '' && strlen($password) < 8) {
                    $errores[] = "La contraseña debe tener al menos 8 caracteres";
                }

                // Debe contener una letra
                if ($password !== '' && !preg_match('/[A-Za-z]/', $password)) {
                    $errores[] = "La contraseña debe contener al menos una letra";
                }

                // Debe contener un número
                if ($password !== '' && !preg_match('/[0-9]/', $password)) {
                    $errores[] = "La contraseña debe contener al menos un número";
                }

                // Debe contener un carácter especial
                if ($password !== '' && !preg_match('/[\W_]/', $password)) {
                    $errores[] = "La contraseña debe contener al menos un carácter especial";
                }

                // Deben coincidir
                if ($password !== '' && $confirmacion !== '' && $password !== $confirmacion) {
                    $errores[] = "Las contraseñas no coinciden";
                }

                // No permitir espacios dentro de la contraseña
                if ($password !== '' && preg_match('/\s/', $password)) {
                    $errores[] = "La contraseña no puede contener espacios";
                }
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
