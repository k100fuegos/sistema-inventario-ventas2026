<?php

require_once dirname(__DIR__) . '/datos/MarcaDatos.php';

class MarcaNegocio
{
    private $marcaDatos;

    public function __construct()
    {
        $this->marcaDatos = new MarcaDatos();
    }

    private function limpiarDatos($datos)
    {
        return [
            'id_marca'     => isset($datos['id_marca']) ? (int) $datos['id_marca'] : null,
            'nombre_marca' => isset($datos['nombre_marca']) ? trim($datos['nombre_marca']) : '',
            'estado_marca' => isset($datos['estado_marca']) ? (int) $datos['estado_marca'] : 1
        ];
    }

    public function listarMarcas()
    {
        return $this->marcaDatos->listarMarcas();
    }

    private function validarMarca($datos)
    {
        $errores = [];

        if (!isset($datos['nombre_marca']) || empty(trim($datos['nombre_marca']))) {
            $errores[] = "El nombre de la marca es obligatorio";
        }

        if (isset($datos['nombre_marca']) && strlen(trim($datos['nombre_marca'])) > 100) {
            $errores[] = "El nombre de la marca no debe superar los 100 caracteres";
        }

        if (isset($datos['estado_marca']) && !in_array((int)$datos['estado_marca'], [0, 1], true)) {
            $errores[] = "El estado de la marca no es válido";
        }

        return $errores;
    }

    public function crearMarca($datos)
    {
        $errores = $this->validarMarca($datos);

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $marca = $this->limpiarDatos($datos);

        // Buscar si ya existe una marca con ese nombre
        $marcaExistente = $this->marcaDatos->obtenerMarcaPorNombre($marca['nombre_marca']);

        if ($marcaExistente) {
            // Existe pero está eliminada -> Reactivar
            if ((int)$marcaExistente['eliminado_marca'] === 1) {
                $marca['id_marca'] = $marcaExistente['id_marca'];
                $resultado = $this->marcaDatos->reactivarMarca($marca);

                return [
                    'exito' => $resultado,
                    'mensaje' => $resultado
                        ? 'La marca ya existía y fue restaurada correctamente.'
                        : 'La marca ya existe y no se pudo restaurar.'
                ];
            }

            // Existe y está activa
            return [
                'exito' => false,
                'errores' => ['Ya existe una marca con ese nombre.']
            ];
        }

        // No existe, insertar normalmente
        $resultado = $this->marcaDatos->insertarMarca($marca);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Marca registrada correctamente' : 'No se pudo registrar la marca'
        ];
    }

    public function obtenerMarcaPorId($idMarca)
    {
        if (!is_numeric($idMarca) || $idMarca <= 0) {
            return null;
        }

        return $this->marcaDatos->obtenerMarcaPorId($idMarca);
    }

    public function actualizarMarca($datos)
    {
        $errores = $this->validarMarca($datos);

        if (!isset($datos['id_marca']) || empty($datos['id_marca'])) {
            $errores[] = "El identificador de la marca es obligatorio";
        }

        // Verificar si existe otra marca con ese nombre
        $marcaExistente = $this->marcaDatos->obtenerMarcaPorNombre(trim($datos['nombre_marca']));

        if ($marcaExistente && $marcaExistente['id_marca'] != $datos['id_marca']) {
            if ((int)$marcaExistente['eliminado_marca'] === 1) {
                $errores[] = "Ya existe una marca eliminada con ese nombre. Si desea utilizarla nuevamente, restáurela creando un nuevo registro.";
            } else {
                $errores[] = "Ya existe una marca con ese nombre.";
            }
        }

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $marca = $this->limpiarDatos($datos);
        $marca['id_marca'] = (int) $datos['id_marca'];

        $resultado = $this->marcaDatos->actualizarMarca($marca);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Marca actualizada correctamente' : 'No se pudo actualizar la marca'
        ];
    }

    public function eliminarMarca($idMarca)
    {
        if (!is_numeric($idMarca) || $idMarca <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'El identificador de la marca no es válido'
            ];
        }

        $resultado = $this->marcaDatos->eliminarMarca($idMarca);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Marca eliminada correctamente' : 'No se pudo eliminar la marca'
        ];
    }
}