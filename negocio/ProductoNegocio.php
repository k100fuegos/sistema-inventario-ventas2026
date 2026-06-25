<?php

require_once dirname(__DIR__) . '/datos/ProductoDatos.php';

class ProductoNegocio
{

    private $productoDatos;

    public function __construct()
    {
        $this->productoDatos = new ProductoDatos();
    }

    public function listarProductos($buscar = '')
    {
        return $this->productoDatos->listarProductos($buscar);
    }

    public function listarProductosActivos()
    {
        return $this->productoDatos->obtenerProductosActivos();
    }

    private function limpiarDatos($datos)
    {
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
            'estado_producto'         => isset($datos['estado_producto']) ? (int) $datos['estado_producto'] : 1
        ];
    }

    private function validarProducto($datos)
    {
        $errores = [];

        if (!isset($datos['codigo_producto']) || empty(trim($datos['codigo_producto']))) {
            $errores[] = "El código del producto es obligatorio.";
        } elseif (strlen(trim($datos['codigo_producto'])) > 50) {
            $errores[] = "El código del producto no debe superar los 50 caracteres.";
        }

        if (!isset($datos['nombre_producto']) || empty(trim($datos['nombre_producto']))) {
            $errores[] = "El nombre del producto es obligatorio.";
        } elseif (strlen(trim($datos['nombre_producto'])) > 150) { // Ajustado a 150 según BD
            $errores[] = "El nombre del producto no debe superar los 150 caracteres.";
        }

        if (!isset($datos['id_marca']) || empty($datos['id_marca']) || !is_numeric($datos['id_marca'])) {
            $errores[] = "Debe seleccionar una marca válida.";
        }

        if (!isset($datos['id_categoria']) || empty($datos['id_categoria']) || !is_numeric($datos['id_categoria'])) {
            $errores[] = "Debe seleccionar una categoría válida.";
        }

        if (!isset($datos['precio_producto']) || $datos['precio_producto'] === '' || !is_numeric($datos['precio_producto'])) {
            $errores[] = "El precio de venta es obligatorio y debe ser numérico.";
        } elseif ((float) $datos['precio_producto'] <= 0) {
            $errores[] = "El precio de venta debe ser mayor que cero.";
        }

        if (!isset($datos['stock_producto']) || $datos['stock_producto'] === '' || !is_numeric($datos['stock_producto'])) {
            $errores[] = "La cantidad de stock es obligatoria y debe ser numérica.";
        } elseif ((int) $datos['stock_producto'] < 0) {
            $errores[] = "La cantidad de stock no puede ser negativa.";
        }

        if (!empty($datos['imagen_producto']) && strlen(trim($datos['imagen_producto'])) > 255) {
            $errores[] = "El nombre de la imagen no debe superar los 255 caracteres.";
        }

        if (isset($datos['estado_producto']) && !in_array((int)$datos['estado_producto'], [0, 1], true)) {
            $errores[] = "El estado del producto no es válido.";
        }

        return $errores;
    }

    public function crearProducto($datos, $archivoImg = null)
    {
        $producto = $this->limpiarDatos($datos);
        $errores = $this->validarProducto($producto);

        // 1. Validar la imagen en memoria (si se envió una)
        $nombreImagenFinal = 'sin-imagen.png';
        $subirArchivo = false;

        if ($archivoImg && $archivoImg['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($archivoImg['error'] !== UPLOAD_ERR_OK) {
                $errores[] = "Ocurrió un error al subir la imagen.";
            } else {
                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
                $extension = strtolower(pathinfo($archivoImg['name'], PATHINFO_EXTENSION));

                if (!in_array($extension, $extensionesPermitidas)) {
                    $errores[] = "La imagen debe tener formato JPG, JPEG, PNG o WEBP.";
                }
                if ($archivoImg['size'] > 2 * 1024 * 1024) {
                    $errores[] = "La imagen no debe superar los 2 MB.";
                }

                if (empty($errores)) {
                    $nombreImagenFinal = 'producto_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
                    $producto['imagen_producto'] = $nombreImagenFinal;
                    $subirArchivo = true; // Bandera para saber que está lista para guardarse
                }
            }
        }

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        // 2. Validación de Código Único y Reactivación en la Base de Datos
        $duplicado = $this->productoDatos->obtenerProductoPorCodigo($producto['codigo_producto']);

        if ($duplicado) {
            if ((int)$duplicado['eliminado_producto'] === 1) {
                $producto['id_producto'] = $duplicado['id_producto'];
                $resultado = $this->productoDatos->reactivarProducto($producto);

                // Si la BD guardó con éxito el registro restaurado, movemos físicamente el archivo
                if ($resultado && $subirArchivo) {
                    $this->guardarArchivoFisico($archivoImg, $nombreImagenFinal);
                }

                return [
                    'exito' => $resultado,
                    'mensaje' => $resultado ? 'El código de producto ya existía y fue restaurado correctamente.' : 'Error al restaurar el producto.'
                ];
            }

            return ['exito' => false, 'errores' => ['Ya existe un producto activo con ese código.']];
        }

        // 3. Inserción normal en la Base de Datos
        $resultado = $this->productoDatos->insertarProducto($producto);

        // Si la inserción fue exitosa, movemos físicamente el archivo al disco
        if ($resultado && $subirArchivo) {
            $this->guardarArchivoFisico($archivoImg, $nombreImagenFinal);
        }

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto registrado correctamente.' : 'No se pudo registrar el producto.'
        ];
    }

    private function guardarArchivoFisico($archivo, $nombreFinal)
    {
        $directorioDestino = dirname(__DIR__) . '/public/img/productos/';
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }
        move_uploaded_file($archivo['tmp_name'], $directorioDestino . $nombreFinal);
    }

    public function obtenerProductoPorId($idProducto)
    {
        if (!is_numeric($idProducto) || $idProducto <= 0) {
            return null;
        }
        return $this->productoDatos->obtenerProductoPorId($idProducto);
    }

    public function actualizarProducto($datos)
    {
        $producto = $this->limpiarDatos($datos);
        $errores = $this->validarProducto($producto);

        if (!isset($producto['id_producto']) || empty($producto['id_producto'])) {
            $errores[] = "El identificador del producto es obligatorio.";
        }

        // Validación de Código Único en actualización
        $duplicado = $this->productoDatos->obtenerProductoPorCodigo($producto['codigo_producto']);

        if ($duplicado && $duplicado['id_producto'] != $producto['id_producto']) {
            if ((int)$duplicado['eliminado_producto'] === 1) {
                $errores[] = "Ya existe un producto eliminado con este código. Si desea utilizarlo, restáurelo creando un nuevo registro.";
            } else {
                $errores[] = "Ya existe un producto con ese código.";
            }
        }

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $resultado = $this->productoDatos->actualizarProducto($producto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto actualizado correctamente.' : 'No se pudo actualizar el producto.'
        ];
    }

    public function eliminarProducto($idProducto)
    {
        if (!is_numeric($idProducto) || $idProducto <= 0) {
            return ['exito' => false, 'mensaje' => 'El identificador del producto no es válido.'];
        }

        // Verificación de integridad referencial
        if ($this->productoDatos->productoTieneVentas($idProducto)) {
            return [
                'exito' => false,
                'mensaje' => 'No se puede eliminar este producto porque ya está incluido en una o más ventas.'
            ];
        }

        $resultado = $this->productoDatos->eliminarProducto($idProducto);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Producto eliminado correctamente.' : 'No se pudo eliminar el producto.'
        ];
    }

    // Método auxiliar privado para destruir el archivo del disco
    private function eliminarArchivoFisico($nombreArchivo) {
        if (empty($nombreArchivo) || $nombreArchivo === 'sin-imagen.png') {
            return; // No intentamos borrar la imagen por defecto
        }

        $ruta = dirname(__DIR__) . '/public/img/productos/' . $nombreArchivo;
        
        if (file_exists($ruta) && is_file($ruta)) {
            unlink($ruta);
        }
    }
}
