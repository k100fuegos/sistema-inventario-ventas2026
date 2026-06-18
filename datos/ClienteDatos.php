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
                      WHERE estado_cliente = '1' 
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

        $conexion->query = "INSERT INTO clientes (nombre_cliente, tipo_cliente, dui_cliente, nit_cliente, nrc_cliente, telefono_cliente, correo_cliente, direccion_cliente, estado_cliente)
                            VALUES (:nombreCliente, :tipoCliente, :duiCliente, :nitCliente, :nrcCliente, :telefonoCliente, :correoCliente, :direccionCliente, 1)";

        return $conexion->execute_query([
            ':nombreCliente'        => $cliente['nombre_cliente'],
            ':tipoCliente'          => $this->valorNulo($cliente['tipo_cliente']),
            ':duiCliente'           => $this->valorNulo($cliente['dui_cliente']),
            ':nitCliente'           => $this->valorNulo($cliente['nit_cliente']),
            ':nrcCliente'           => $this->valorNulo($cliente['nrc_cliente']),
            ':telefonoCliente'      => $this->valorNulo($cliente['telefono_cliente']),
            ':correoCliente'        => $this->valorNulo($cliente['correo_cliente']),
            ':direccionCliente'     => $this->valorNulo($cliente['direccion_cliente']),
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
                          direccion_cliente = :direccionCliente 
                      WHERE id_cliente = :idCliente";

        return $conexion->execute_query([
            ':nombreCliente'        => $cliente['nombre_cliente'],
            ':tipoCliente'          => $this->valorNulo($cliente['tipo_cliente']),
            ':duiCliente'           => $this->valorNulo($cliente['dui_cliente']),
            ':nitCliente'           => $this->valorNulo($cliente['nit_cliente']),
            ':nrcCliente'           => $this->valorNulo($cliente['nrc_cliente']),
            ':telefonoCliente'      => $this->valorNulo($cliente['telefono_cliente']),
            ':correoCliente'        => $this->valorNulo($cliente['correo_cliente']),
            ':direccionCliente'     => $this->valorNulo($cliente['direccion_cliente']),
            ':idCliente'            => $cliente['id_cliente']
        ]);
    }

    public function obtenerClientePorId($idCliente)
    {
        $conexion = new Conexion();
        $conexion->query = "SELECT id_cliente, nombre_cliente, tipo_cliente, dui_cliente, nit_cliente, nrc_cliente, 
                            telefono_cliente, correo_cliente, direccion_cliente, estado_cliente
                      FROM clientes 
                      WHERE id_cliente = :idCliente
                      AND estado_cliente = '1'";
        return $conexion->get_record([
            ':idCliente' => $idCliente
        ]);
    }

    public function eliminarCliente($idCliente)
    {
        $conexion = new Conexion();
        $conexion->query = "UPDATE clientes 
                      SET estado_cliente = '0' 
                      WHERE id_cliente = :idCliente";

        return $conexion->execute_query([
            ':idCliente' => $idCliente
        ]);
    }
}
