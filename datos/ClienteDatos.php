<?php

require_once __DIR__ . '/Conexion.php';

class ClienteDatos
{
    public function listarClientes()
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT id_cliente, nombre_cliente, tipo_cliente, dui_cliente, nit_cliente, nrc_cliente, 
                                   telefono_cliente, correo_cliente, direccion_cliente, estado_cliente
                            FROM clientes 
                            WHERE eliminado_cliente = 0
                            ORDER BY nombre_cliente ASC";
                      
        return $conexion->get_records();
    }

    private function valorNulo($valor)
    {
        return trim($valor) == '' ? null : trim($valor);
    }

    public function insertarCliente($cliente)
    {
        $conexion = new Conexion();

        $conexion->query = "INSERT INTO clientes 
                            (nombre_cliente, tipo_cliente, dui_cliente, nit_cliente, nrc_cliente, telefono_cliente, correo_cliente, direccion_cliente, estado_cliente, eliminado_cliente)
                            VALUES 
                            (:nombreCliente, :tipoCliente, :duiCliente, :nitCliente, :nrcCliente, :telefonoCliente, :correoCliente, :direccionCliente, :estadoCliente, 0)";

        return $conexion->execute_query([
            ':nombreCliente'    => $cliente['nombre_cliente'],
            ':tipoCliente'      => $this->valorNulo($cliente['tipo_cliente']),
            ':duiCliente'       => $this->valorNulo($cliente['dui_cliente']),
            ':nitCliente'       => $this->valorNulo($cliente['nit_cliente']),
            ':nrcCliente'       => $this->valorNulo($cliente['nrc_cliente']),
            ':telefonoCliente'  => $this->valorNulo($cliente['telefono_cliente']),
            ':correoCliente'    => $this->valorNulo($cliente['correo_cliente']),
            ':direccionCliente' => $this->valorNulo($cliente['direccion_cliente']),
            ':estadoCliente'    => $cliente['estado_cliente']
        ]);
    }

    public function actualizarCliente($cliente)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE clientes 
                            SET nombre_cliente = :nombreCliente, 
                                tipo_cliente = :tipoCliente, 
                                dui_cliente = :duiCliente, 
                                nit_cliente = :nitCliente,
                                nrc_cliente = :nrcCliente, 
                                telefono_cliente = :telefonoCliente, 
                                correo_cliente = :correoCliente, 
                                direccion_cliente = :direccionCliente,
                                estado_cliente = :estadoCliente
                            WHERE id_cliente = :idCliente
                            AND eliminado_cliente = 0";

        return $conexion->execute_query([
            ':nombreCliente'    => $cliente['nombre_cliente'],
            ':tipoCliente'      => $this->valorNulo($cliente['tipo_cliente']),
            ':duiCliente'       => $this->valorNulo($cliente['dui_cliente']),
            ':nitCliente'       => $this->valorNulo($cliente['nit_cliente']),
            ':nrcCliente'       => $this->valorNulo($cliente['nrc_cliente']),
            ':telefonoCliente'  => $this->valorNulo($cliente['telefono_cliente']),
            ':correoCliente'    => $this->valorNulo($cliente['correo_cliente']),
            ':direccionCliente' => $this->valorNulo($cliente['direccion_cliente']),
            ':estadoCliente'    => $cliente['estado_cliente'],
            ':idCliente'        => $cliente['id_cliente']
        ]);
    }

    public function obtenerClientePorId($idCliente)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT id_cliente, nombre_cliente, tipo_cliente, dui_cliente, nit_cliente, nrc_cliente, 
                                   telefono_cliente, correo_cliente, direccion_cliente, estado_cliente
                            FROM clientes 
                            WHERE id_cliente = :idCliente
                            AND eliminado_cliente = 0
                            LIMIT 1";

        return $conexion->get_record([':idCliente' => $idCliente]);
    }



    public function obtenerClientePorDui($dui)
    {
        if (empty($dui)) return null;
        $conexion = new Conexion();
        $conexion->query = "SELECT id_cliente, eliminado_cliente FROM clientes WHERE dui_cliente = :dui LIMIT 1";
        return $conexion->get_record([':dui' => $dui]);
    }

    public function obtenerClientePorNit($nit)
    {
        if (empty($nit)) return null;
        $conexion = new Conexion();
        $conexion->query = "SELECT id_cliente, eliminado_cliente FROM clientes WHERE nit_cliente = :nit LIMIT 1";
        return $conexion->get_record([':nit' => $nit]);
    }

    public function obtenerClientePorNrc($nrc)
    {
        if (empty($nrc)) return null;
        $conexion = new Conexion();
        $conexion->query = "SELECT id_cliente, eliminado_cliente FROM clientes WHERE nrc_cliente = :nrc LIMIT 1";
        return $conexion->get_record([':nrc' => $nrc]);
    }



    public function reactivarCliente($cliente)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE clientes 
                            SET nombre_cliente = :nombreCliente, 
                                tipo_cliente = :tipoCliente, 
                                dui_cliente = :duiCliente, 
                                nit_cliente = :nitCliente,
                                nrc_cliente = :nrcCliente, 
                                telefono_cliente = :telefonoCliente, 
                                correo_cliente = :correoCliente, 
                                direccion_cliente = :direccionCliente,
                                estado_cliente = :estadoCliente,
                                eliminado_cliente = 0
                            WHERE id_cliente = :idCliente";

        return $conexion->execute_query([
            ':nombreCliente'    => $cliente['nombre_cliente'],
            ':tipoCliente'      => $this->valorNulo($cliente['tipo_cliente']),
            ':duiCliente'       => $this->valorNulo($cliente['dui_cliente']),
            ':nitCliente'       => $this->valorNulo($cliente['nit_cliente']),
            ':nrcCliente'       => $this->valorNulo($cliente['nrc_cliente']),
            ':telefonoCliente'  => $this->valorNulo($cliente['telefono_cliente']),
            ':correoCliente'    => $this->valorNulo($cliente['correo_cliente']),
            ':direccionCliente' => $this->valorNulo($cliente['direccion_cliente']),
            ':estadoCliente'    => $cliente['estado_cliente'],
            ':idCliente'        => $cliente['id_cliente']
        ]);
    }



    public function eliminarCliente($idCliente)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE clientes SET eliminado_cliente = 1 WHERE id_cliente = :idCliente";
        return $conexion->execute_query([':idCliente' => $idCliente]);
    }

    public function clienteTieneVentas($idCliente)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT COUNT(*) AS total FROM ventas WHERE id_cliente = :idCliente";
        $resultado = $conexion->get_record([':idCliente' => $idCliente]);
        return $resultado['total'] > 0;
    }
}