<?php

require_once dirname(__DIR__) . '/datos/CategoriaDatos.php';

class CategoriaNegocio
{

    private $categoriaDatos;

    public function __construct()
    {
        $this->categoriaDatos = new CategoriaDatos();
    }

    private function limpiarDatos($datos)
    {
        return [
            'id_categoria' => isset($datos['id_categoria']) ? (int) $datos['id_categoria'] : null,
            'nombre_categoria' => isset($datos['nombre_categoria']) ? trim($datos['nombre_categoria']) : '',
            'descripcion_categoria' => isset($datos['descripcion_categoria']) ? trim($datos['descripcion_categoria']) : '',
            'estado_categoria' => isset($datos['estado_categoria']) ? (int) $datos['estado_categoria'] : 1
        ];
    }

    public function listarCategorias($buscar = '')
    {
        return $this->categoriaDatos->listarCategorias($buscar);
    }

    private function validarCategoria($datos)
    {
        $errores = [];

        if (!isset($datos['nombre_categoria']) || empty(trim($datos['nombre_categoria']))) {
            $errores[] = "El nombre de la categoría es obligatorio";
        }

        if (isset($datos['nombre_categoria']) && strlen(trim($datos['nombre_categoria'])) > 100) {
            $errores[] = "El nombre de la categoría no debe superar los 100 caracteres";
        }

        if (isset($datos['descripcion_categoria']) && strlen(trim($datos['descripcion_categoria'])) > 255) {
            $errores[] = "La descripción no puede superar los 255 caracteres";
        }

        if (isset($datos['estado_categoria']) && !in_array((int)$datos['estado_categoria'], [0, 1], true)) {
            $errores[] = "El estado de la categoría no es válido";
        }

        return $errores;
    }

    public function crearCategoria($datos)
    {
        $errores = $this->validarCategoria($datos);

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $categoria = $this->limpiarDatos($datos);

        // Buscar si ya existe una categoría con ese nombre
        $categoriaExistente = $this->categoriaDatos->obtenerCategoriaPorNombre(
            $categoria['nombre_categoria']
        );

        if ($categoriaExistente) {

            // Existe pero está eliminada -> Reactivar
            if ((int)$categoriaExistente['eliminado_categoria'] === 1) {

                $categoria['id_categoria'] = $categoriaExistente['id_categoria'];

                $resultado = $this->categoriaDatos->reactivarCategoria($categoria);

                return [
                    'exito' => $resultado,
                    'mensaje' => $resultado
                        ? 'La categoría ya existía y fue restaurada correctamente.'
                        : 'La categoría ya existe y no se pudo restaurar la categoría.'
                ];
            }

            // Existe y está activa
            return [
                'exito' => false,
                'errores' => [
                    'Ya existe una categoría con ese nombre.'
                ]
            ];
        }

        // No existe, insertar normalmente
        $resultado = $this->categoriaDatos->insertarCategoria($categoria);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado
                ? 'Categoría registrada correctamente.'
                : 'No se pudo registrar la categoría.'
        ];
    }

    public function obtenerCategoriaPorId($idCategoria)
    {
        if (!is_numeric($idCategoria) || $idCategoria <= 0) {
            return null;
        }

        return $this->categoriaDatos->obtenerCategoriaPorId($idCategoria);
    }

    public function actualizarCategoria($datos)
    {
        $errores = $this->validarCategoria($datos);

        if (!isset($datos['id_categoria']) || empty($datos['id_categoria'])) {
            $errores[] = "El identificador de la categoría es obligatorio";
        }

        // Verificar si existe otra categoría con ese nombre
        $categoriaExistente = $this->categoriaDatos->obtenerCategoriaPorNombre(trim($datos['nombre_categoria']));

        if (
            $categoriaExistente &&
            $categoriaExistente['id_categoria'] != $datos['id_categoria']
        ) {

            if ((int)$categoriaExistente['eliminado_categoria'] === 1) {
                $errores[] = "Ya existe una categoría eliminada con ese nombre. Si desea utilizarla nuevamente, restáurela creando un nuevo registro";
            } else {
                $errores[] = "Ya existe una categoría activa con ese nombre.";
            }
        }

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $categoria = $this->limpiarDatos($datos);
        $categoria['id_categoria'] = (int)$datos['id_categoria'];

        $resultado = $this->categoriaDatos->actualizarCategoria($categoria);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado
                ? 'Categoría actualizada correctamente'
                : 'No se pudo actualizar la categoría'
        ];
    }

    public function eliminarCategoria($idCategoria)
    {
        if (!is_numeric($idCategoria) || $idCategoria <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'El identificador de la categoría no es válido'
            ];
        }

        $resultado = $this->categoriaDatos->eliminarCategoria($idCategoria);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Categoría eliminada correctamente' : 'No se pudo eliminar la categoría'
        ];
    }
}
