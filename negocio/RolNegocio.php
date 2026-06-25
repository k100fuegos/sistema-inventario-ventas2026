<?php

require_once dirname(__DIR__) . '/datos/RolDatos.php';

class RolNegocio
{

    private $rolDatos;

    public function __construct()
    {
        $this->rolDatos = new RolDatos();
    }

    public function listarRoles()
    {
        return $this->rolDatos->listarRoles();
    }

    private function limpiarDatos($datos)
    {
        return [
            'id_rol'     => isset($datos['id_rol']) ? (int) $datos['id_rol'] : null,
            'nombre_rol' => isset($datos['nombre_rol']) ? trim($datos['nombre_rol']) : '',
            'estado_rol' => isset($datos['estado_rol']) ? (int) $datos['estado_rol'] : 1
        ];
    }

    private function validarRol($datos)
    {
        $errores = [];

        // Validación del Nombre del Rol
        if (!isset($datos['nombre_rol']) || empty(trim($datos['nombre_rol']))) {
            $errores[] = "El nombre del rol es obligatorio";
        } elseif (strlen(trim($datos['nombre_rol'])) > 50) {
            $errores[] = "El nombre del rol no debe superar los 50 caracteres";
        }

        // Validación del Estado (por si se edita desde un panel de administración)
        if (isset($datos['estado_rol']) && !in_array((int)$datos['estado_rol'], [0, 1], true)) {
            $errores[] = "El estado del rol no es válido";
        }

        return $errores;
    }

    public function crearRol($datos)
    {
        $errores = $this->validarRol($datos);

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $rol = $this->limpiarDatos($datos);
        $resultado = $this->rolDatos->insertarRol($rol);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Rol registrado correctamente' : 'No se pudo registrar el rol'
        ];
    }

    public function obtenerRolPorId($idRol)
    {
        if (!is_numeric($idRol) || $idRol <= 0) {
            return null;
        }

        return $this->rolDatos->obtenerRolPorId($idRol);
    }

    public function actualizarRol($datos)
    {
        $errores = $this->validarRol($datos);

        if (!isset($datos['id_rol']) || empty($datos['id_rol'])) {
            $errores[] = "El identificador del rol es obligatorio";
        }

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $rol = $this->limpiarDatos($datos);
        $rol['id_rol'] = (int) $datos['id_rol'];

        $resultado = $this->rolDatos->actualizarRol($rol);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Rol actualizado correctamente' : 'No se pudo actualizar el rol'
        ];
    }

    public function eliminarRol($idRol)
    {
        if (!is_numeric($idRol) || $idRol <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'El identificador del rol no es válido'
            ];
        }

        $resultado = $this->rolDatos->eliminarRol($idRol);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Rol eliminado correctamente' : 'No se pudo eliminar el rol'
        ];
    }
}