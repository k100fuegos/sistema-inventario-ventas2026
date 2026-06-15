<?php

require_once dirname(__DIR__) . '/datos/ProductoDatos.php';

class ProductoNegocio {

    private $productoDatos;

    public function __construct() {
        $this->productoDatos = new ProductoDatos();
    }

    public function listarProductos() {
        return $this->productoDatos->listarProductos();
    }

    private function limpiarDatos($datos) {
        return [
            'id_producto'             => isset($datos['id_producto']) ? (int) $datos['id_producto'] : null,
            'codigo_producto'         => isset($datos['codigo_producto']) ? trim($datos['codigo_producto']) : '',
            'nombre_producto'         => trim($datos['nombre_producto']),
            'modelo_producto'         => isset($datos['modelo_producto']) ? trim($datos['modelo_producto']) : '',
            'descripcion_producto'    => isset($datos['descripcion_producto']) ? trim($datos['descripcion_producto']) : '',
            'imagen_producto'         => isset($datos['imagen_producto']) ? trim($datos['imagen_producto']) : 'sin-imagen.png',
            'id_marca'                => (int) $datos['id_marca'],
            'id_categoria'            => (int) $datos['id_categoria'],
            'precio_producto'         => number_format((float) $datos['precio_producto'], 2, '.', ''),
            'stock_producto'          => (int) $datos['stock_producto'],
            'estado_producto'         => $datos['estado_producto']];
    }

    private function validarProducto($datos) {
        $errores = [];

        if (!isset($datos['codigo_producto']) || empty(trim($datos['codigo_producto']))) {
            $errores[] = "El código del producto es obligatorio";
        }

        if (isset($datos['codigo_producto']) && strlen(trim($datos['codigo_producto'])) > 50) {
            $errores[] = "El código del producto no debe superar los 50 caracteres";
        }

        if (!isset($datos['nombre_producto']) || empty(trim($datos['nombre_producto']))) {
            $errores[] = "El nombre del producto es obligatorio";
        }

        if (isset($datos['nombre_producto']) && strlen(trim($datos['nombre_producto'])) > 255) {
            $errores[] = "El nombre del producto no debe superar los 255 caracteres";
        }

        if (!isset($datos['id_marca']) || empty($datos['id_marca']) || !is_numeric($datos['id_marca'])) {
            $errores[] = "Debe seleccionar una categoría válida";
        }

        if (!isset($datos['id_categoria']) || empty($datos['id_categoria']) || !is_numeric($datos['id_categoria'])) {
            $errores[] = "Debe seleccionar una categoría válida";
        }

        if (!isset($datos['precio_producto']) || $datos['precio_producto'] === '' || !is_numeric($datos['precio_producto'])) {
            $errores[] = "El precio de venta es obligatorio y debe ser numérico";
        } elseif ((float) $datos['precio_producto'] <= 0) {
            $errores[] = "El precio de venta debe ser mayor que cero";
        }

        if (!isset($datos['stock_producto']) || $datos['stock_producto'] === '' || !is_numeric($datos['stock_producto'])) {
            $errores[] = "La cantidad de stock es obligatoria y debe ser numérica";
        } elseif ((int) $datos['stock_producto'] < 0) {
            $errores[] = "La cantidad de stock no puede ser negativa";
        }

        if (!empty($datos['imagen_producto']) && strlen(trim($datos['imagen_producto'])) > 255) {
            $errores[] = "El nombre de la imagen no debe superar los 255 caracteres.";
        }
        return $errores;
    }

    public function crearProducto($datos) {
         $errores = $this->validarProducto($datos);

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $producto = $this->limpiarDatos($datos);
        $resultado = $this->productoDatos->insertarProducto($producto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto registrado correctamente' : 'No se pudo registrar el producto'
        ];
    }

    public function obtenerProductoPorId($idProducto) {
        if (!is_numeric($idProducto) || $idProducto <= 0) {
            return null;
        }

        return $this->productoDatos->obtenerProductoPorId($idProducto);
    }

    public function actualizarProducto($datos) {
        $errores = $this->validarProducto($datos);

        if (!isset($datos['id_producto']) || empty($datos['id_producto'])) {
            $errores[] = "El identificador del producto es obligatorio";
        }

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $producto = $this->limpiarDatos($datos);
        $resultado = $this->productoDatos->actualizarProducto($producto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto actualizado correctamente' : 'No se pudo actualizar el producto'
        ];
    }

    public function eliminarProducto($idProducto) {
        if (!is_numeric($idProducto) || $idProducto <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'El identificador del producto no es válido'
            ];
        }

        $resultado = $this->productoDatos->eliminarProducto($idProducto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto eliminado correctamente' : 'No se pudo eliminar el producto'
        ];
    }
}
