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
            'id_marca' => isset($datos['id_marca']) ? (int) $datos['id_marca'] : null,
            'nombre_marca' => isset($datos['nombre_marca']) ? trim($datos['nombre_marca']) : '',
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
