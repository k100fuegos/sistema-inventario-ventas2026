<?php

require_once dirname(__DIR__) . '/datos/ClienteDatos.php';

class ClienteNegocio
{
    private $clienteDatos;

    public function __construct()
    {
        $this->clienteDatos = new ClienteDatos();
    }

    public function listarClientes($buscar = '')
    {
        return $this->clienteDatos->listarClientes($buscar);
    }

    private function limpiarDatos($datos)
    {
        return [
            'id_cliente'        => isset($datos['id_cliente']) ? (int) $datos['id_cliente'] : null,
            'nombre_cliente'    => isset($datos['nombre_cliente']) ? trim($datos['nombre_cliente']) : '',
            'tipo_cliente'      => isset($datos['tipo_cliente']) ? trim($datos['tipo_cliente']) : 'PN',
            'dui_cliente'       => isset($datos['dui_cliente']) ? trim($datos['dui_cliente']) : null,
            'nit_cliente'       => isset($datos['nit_cliente']) ? trim($datos['nit_cliente']) : null,
            'nrc_cliente'       => isset($datos['nrc_cliente']) ? trim($datos['nrc_cliente']) : null,
            'telefono_cliente'  => isset($datos['telefono_cliente']) ? trim($datos['telefono_cliente']) : null,
            'correo_cliente'    => isset($datos['correo_cliente']) ? trim($datos['correo_cliente']) : null,
            'direccion_cliente' => isset($datos['direccion_cliente']) ? trim($datos['direccion_cliente']) : null,
            'estado_cliente'    => isset($datos['estado_cliente']) ? (int) $datos['estado_cliente'] : 1
        ];
    }

    private function validarCliente($datos)
    {
        $errores = [];

        // Validar Nombre
        if (empty($datos['nombre_cliente'])) {
            $errores[] = "El nombre del cliente es obligatorio.";
        } elseif (strlen($datos['nombre_cliente']) > 150) {
            $errores[] = "El nombre del cliente no debe superar los 150 caracteres.";
        }

        // Tipo Cliente
        if (!in_array($datos['tipo_cliente'], ['PN', 'PJ'], true)) {
            $errores[] = "El tipo de cliente seleccionado no es válido.";
        }

        // Validaciones con Expresiones Regulares
        if (!empty($datos['dui_cliente']) && !preg_match('/^\d{8}-\d$/', $datos['dui_cliente'])) {
            $errores[] = "El formato del DUI es incorrecto (Ej. 00000000-0).";
        }

        if (!empty($datos['nit_cliente']) && !preg_match('/^\d{4}-\d{6}-\d{3}-\d$/', $datos['nit_cliente'])) {
            $errores[] = "El formato del NIT es incorrecto (Ej. 0000-000000-000-0).";
        }

        if (!empty($datos['nrc_cliente']) && !preg_match('/^[\w\-]+$/', $datos['nrc_cliente'])) {
            $errores[] = "El formato del NRC contiene caracteres no permitidos.";
        }

        if (!empty($datos['telefono_cliente']) && !preg_match('/^\d{4}-\d{4}$/', $datos['telefono_cliente'])) {
            $errores[] = "El teléfono debe tener el formato 0000-0000.";
        }

        if (!empty($datos['correo_cliente']) && !filter_var($datos['correo_cliente'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico proporcionado no es válido.";
        }

        if (!empty($datos['direccion_cliente']) && strlen($datos['direccion_cliente']) > 255) {
            $errores[] = "La dirección no debe superar los 255 caracteres.";
        }

        if (!in_array((int)$datos['estado_cliente'], [0, 1], true)) {
            $errores[] = "El estado del cliente no es válido.";
        }

        // Reglas de obligatoriedad según Tipo
        if ($datos['tipo_cliente'] === 'PN' && empty($datos['dui_cliente'])) {
            $errores[] = "El DUI es obligatorio para una Persona Natural.";
        }

        if ($datos['tipo_cliente'] === 'PJ') {
            if (empty($datos['nit_cliente'])) $errores[] = "El NIT es obligatorio para una Persona Jurídica.";
            if (empty($datos['nrc_cliente'])) $errores[] = "El NRC es obligatorio para una Persona Jurídica.";
        }

        return $errores;
    }

    public function crearCliente($datos)
    {
        $cliente = $this->limpiarDatos($datos);
        $errores = $this->validarCliente($cliente);

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $idReactivar = null;

        // Validaciones independientes de documentos en Creación
        if ($cliente['dui_cliente']) {
            $dup = $this->clienteDatos->obtenerClientePorDui($cliente['dui_cliente']);
            if ($dup) {
                if ($dup['eliminado_cliente'] == 0) $errores[] = "El DUI ingresado ya le pertenece a otro cliente.";
                else $idReactivar = $dup['id_cliente'];
            }
        }

        if ($cliente['nit_cliente']) {
            $dup = $this->clienteDatos->obtenerClientePorNit($cliente['nit_cliente']);
            if ($dup) {
                if ($dup['eliminado_cliente'] == 0) $errores[] = "El NIT ingresado ya le pertenece a otro cliente.";
                else {
                    if ($idReactivar && $idReactivar != $dup['id_cliente']) $errores[] = "Conflicto: El DUI y NIT ingresados pertenecen a registros eliminados diferentes.";
                    $idReactivar = $dup['id_cliente'];
                }
            }
        }

        if ($cliente['nrc_cliente']) {
            $dup = $this->clienteDatos->obtenerClientePorNrc($cliente['nrc_cliente']);
            if ($dup) {
                if ($dup['eliminado_cliente'] == 0) $errores[] = "El NRC ingresado ya le pertenece a otro cliente.";
                else {
                    if ($idReactivar && $idReactivar != $dup['id_cliente']) $errores[] = "Conflicto: El NRC pertenece a un registro eliminado distinto al DUI o NIT.";
                    $idReactivar = $dup['id_cliente'];
                }
            }
        }

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        // Restauración automática si se encontraron los documentos eliminados
        if ($idReactivar) {
            $cliente['id_cliente'] = $idReactivar;
            $resultado = $this->clienteDatos->reactivarCliente($cliente);
            return [
                'exito' => $resultado,
                'mensaje' => $resultado ? 'El cliente ya existía en los registros y fue restaurado correctamente.' : 'Error al restaurar el cliente.'
            ];
        }

        $resultado = $this->clienteDatos->insertarCliente($cliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente registrado correctamente.' : 'No se pudo registrar el cliente.'
        ];
    }

    public function obtenerClientePorId($idCliente)
    {
        if (!is_numeric($idCliente) || $idCliente <= 0) return null;
        return $this->clienteDatos->obtenerClientePorId($idCliente);
    }

    public function actualizarCliente($datos)
    {
        $cliente = $this->limpiarDatos($datos);
        $errores = $this->validarCliente($cliente);

        if (empty($cliente['id_cliente'])) {
            $errores[] = "El identificador del cliente es obligatorio.";
        }

        // Validaciones independientes de documentos en Actualización
        if ($cliente['dui_cliente']) {
            $dup = $this->clienteDatos->obtenerClientePorDui($cliente['dui_cliente']);
            if ($dup && $dup['id_cliente'] != $cliente['id_cliente']) {
                if ($dup['eliminado_cliente'] == 1) $errores[] = "El DUI ingresado pertenece a un registro que fue eliminado del sistema, Si realmente desea utilizarlo cree un nuevo cliente";
                else $errores[] = "El DUI ingresado ya está en uso por otro cliente.";
            }
        }

        if ($cliente['nit_cliente']) {
            $dup = $this->clienteDatos->obtenerClientePorNit($cliente['nit_cliente']);
            if ($dup && $dup['id_cliente'] != $cliente['id_cliente']) {
                if ($dup['eliminado_cliente'] == 1) $errores[] = "No puedes usar este NIT. Pertenece a un registro que fue eliminado del sistema.";
                else $errores[] = "El NIT ingresado ya está en uso por otro cliente.";
            }
        }

        if ($cliente['nrc_cliente']) {
            $dup = $this->clienteDatos->obtenerClientePorNrc($cliente['nrc_cliente']);
            if ($dup && $dup['id_cliente'] != $cliente['id_cliente']) {
                if ($dup['eliminado_cliente'] == 1) $errores[] = "No puedes usar este NRC. Pertenece a un registro que fue eliminado del sistema.";
                else $errores[] = "El NRC ingresado ya está en uso por otro cliente.";
            }
        }

        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        $resultado = $this->clienteDatos->actualizarCliente($cliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente actualizado correctamente.' : 'No se pudo actualizar el cliente.'
        ];
    }

    public function eliminarCliente($idCliente)
    {
        if (!is_numeric($idCliente) || $idCliente <= 0) {
            return ['exito' => false, 'mensaje' => 'El identificador del cliente no es válido.'];
        }

        // Verificación de ventas antes de eliminar
        if ($this->clienteDatos->clienteTieneVentas($idCliente)) {
            return [
                'exito' => false, 
                'mensaje' => 'No se puede eliminar este cliente porque tiene ventas asociadas en el sistema.'
            ];
        }

        $resultado = $this->clienteDatos->eliminarCliente($idCliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente eliminado correctamente.' : 'No se pudo eliminar el cliente.'
        ];
    }
}