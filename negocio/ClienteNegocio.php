<?php

require_once dirname(__DIR__) . '/datos/ClienteDatos.php';

class ClienteNegocio
{

    private $clienteDatos;

    public function __construct()
    {
        $this->clienteDatos = new ClienteDatos();
    }

    public function listarClientes()
    {
        return $this->clienteDatos->listarClientes();
    }

    private function limpiarDatos($datos)
    {
        return [
            'id_cliente' => isset($datos['id_cliente']) ? (int) $datos['id_cliente'] : null,

            'nombre_cliente' => isset($datos['nombre_cliente']) ? trim($datos['nombre_cliente']) : '',

            'tipo_cliente' => isset($datos['tipo_cliente']) ? trim($datos['tipo_cliente']) : 'PN',

            'dui_cliente' => isset($datos['dui_cliente']) ? trim($datos['dui_cliente']) : null,

            'nit_cliente' => isset($datos['nit_cliente']) ? trim($datos['nit_cliente']) : null,

            'nrc_cliente' => isset($datos['nrc_cliente']) ? trim($datos['nrc_cliente']) : null,

            'telefono_cliente' => isset($datos['telefono_cliente']) ? trim($datos['telefono_cliente']) : null,

            'correo_cliente' => isset($datos['correo_cliente']) ? trim($datos['correo_cliente']) : null,

            'direccion_cliente' => isset($datos['direccion_cliente']) ? trim($datos['direccion_cliente']) : null,

            'estado_cliente' => isset($datos['estado_cliente']) ? (int) $datos['estado_cliente'] : 1
        ];
    }

    private function validarCliente($datos)
    {
        $errores = [];

        if (!isset($datos['nombre_cliente']) || empty(trim($datos['nombre_cliente']))) {
            $errores[] = "El nombre del cliente es obligatorio";
        }

        if (isset($datos['nombre_cliente']) && strlen(trim($datos['nombre_cliente'])) > 150) {
            $errores[] = "El nombre del cliente no debe superar los 150 caracteres";
        }

        if (!empty($datos['nit_cliente']) && strlen(trim($datos['nit_cliente'])) > 17) {
            $errores[] = "El NIT no debe superar los 17 caracteres";
        }

        if (!empty($datos['nrc_cliente']) && strlen(trim($datos['nrc_cliente'])) > 20) {
            $errores[] = "El NRC no debe superar los 20 caracteres";
        }

        if (!empty($datos['correo_cliente']) && !filter_var(trim($datos['correo_cliente']), FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido";
        }

        if (!empty($datos['direccion_cliente']) && strlen(trim($datos['direccion_cliente'])) > 255) {
            $errores[] = "La dirección no debe superar los 255 caracteres";
        }

        if (isset($datos['tipo_cliente']) && !in_array($datos['tipo_cliente'], ['PN', 'PJ'], true)) {
            $errores[] = "El tipo de cliente no es válido";
        }

        if (isset($datos['tipo_cliente'])) {

            if ($datos['tipo_cliente'] === 'PN' && empty(trim($datos['dui_cliente'] ?? ''))) {
                $errores[] = "El DUI es obligatorio para una Persona Natural.";
            }

            if ($datos['tipo_cliente'] === 'PJ') {

                if (empty(trim($datos['nit_cliente'] ?? ''))) {
                    $errores[] = "El NIT es obligatorio para una Persona Jurídica.";
                }

                if (empty(trim($datos['nrc_cliente'] ?? ''))) {
                    $errores[] = "El NRC es obligatorio para una Persona Jurídica.";
                }
            }
        }

        if (isset($datos['estado_cliente']) && !in_array((int)$datos['estado_cliente'], [0, 1], true)) {
            $errores[] = "El estado del cliente no es válido";
        }

        return $errores;
    }

    public function crearCliente($datos)
    {
        $errores = $this->validarCliente($datos);

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $cliente = $this->limpiarDatos($datos);
        $resultado = $this->clienteDatos->insertarCliente($cliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente registrado correctamente' : 'No se pudo registrar el cliente'
        ];
    }

    public function obtenerClientePorId($idCliente)
    {
        if (!is_numeric($idCliente) || $idCliente <= 0) {
            return null;
        }

        return $this->clienteDatos->obtenerClientePorId($idCliente);
    }

    public function actualizarCliente($datos)
    {
        $errores = $this->validarCliente($datos);

        if (!isset($datos['id_cliente']) || empty($datos['id_cliente'])) {
            $errores[] = "El identificador del cliente es obligatorio";
        }

        if (!empty($errores)) {
            return [
                'exito' => false,
                'errores' => $errores
            ];
        }

        $cliente = $this->limpiarDatos($datos);
        $cliente['id_cliente'] = (int) $datos['id_cliente'];

        $resultado = $this->clienteDatos->actualizarCliente($cliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente actualizado correctamente' : 'No se pudo actualizar el cliente'
        ];
    }

    public function eliminarCliente($idCliente)
    {
        if (!is_numeric($idCliente) || $idCliente <= 0) {
            return [
                'exito' => false,
                'mensaje' => 'El identificador del cliente no es válido'
            ];
        }

        $resultado = $this->clienteDatos->eliminarCliente($idCliente);

        return [
            'exito' => $resultado,
            'mensaje' => $resultado ? 'Cliente eliminado correctamente' : 'No se pudo eliminar el cliente'
        ];
    }
}
