<?php

require_once dirname(__DIR__) . '/datos/RolDatos.php';

class RolNegocio {

    private $rolDatos;

    public function __construct() {
        $this->rolDatos = new RolDatos();
    }

    public function listarRoles() {
        return $this->rolDatos->listarRoles();
    }
}
