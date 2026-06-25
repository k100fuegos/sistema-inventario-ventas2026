<?php

require_once __DIR__ . '/../config/env.php';

/**
 * Clase Conexion
 *
 * Esta clase pertenece a la capa de datos.
 * Se encarga de establecer la conexión con la base de datos
 * utilizando PDO y las variables definidas en el archivo .env.
 */
class Conexion
{
    private $server;
    private $user;
    private $password;
    private $database;
    private $charset;
    private $connection;

    public $query;
    public $record_count;

    /**
     * Constructor de la clase.
     *
     * Obtiene los datos de conexión desde el archivo .env.
     */
    public function __construct()
    {
        $this->server   = $_ENV['DB_HOST'];
        $this->user     = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->database = $_ENV['DB_NAME'];
        $this->charset  = $_ENV['DB_CHARSET'];
    }

    /**
     * Crea una conexión PDO con la base de datos.
     *
     * @return PDO
     */
    private function create_connection()
    {
        try {
            $dns = "mysql:host={$this->server};dbname={$this->database};charset={$this->charset}";

            $this->connection = new PDO(
                $dns,
                $this->user,
                $this->password
            );

            // Configuración de errores mediante excepciones.
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Retorno asociativo por defecto.
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(404);
            require_once __DIR__ . '/../404.php';
            die();
        }

        return $this->connection;
    }

    /**
     * Cierra la conexión con la base de datos.
     */
    private function close_connection()
    {
        $this->connection = null;
    }

    /**
     * Ejecuta consultas INSERT, UPDATE o DELETE.
     *
     * @param array $params Parámetros de la consulta preparada.
     * @return bool
     */
    public function execute_query($params = [])
    {
        try {
            $stmt = $this->create_connection()->prepare($this->query);
            $result = $stmt->execute($params);

            $this->close_connection();

            return $result;
        } catch (PDOException $e) {
            http_response_code(404);
            require_once __DIR__ . '/../404.php';
            die();
        }
    }

    /**
     * Obtiene varios registros mediante una consulta SELECT.
     *
     * @param array $params Parámetros de la consulta preparada.
     * @return array
     */
    public function get_records($params = [])
    {
        try {
            $stmt = $this->create_connection()->prepare($this->query);
            $stmt->execute($params);

            $records = $stmt->fetchAll();
            $this->record_count = count($records);

            $this->close_connection();

            return $records;
        } catch (PDOException $e) {
            http_response_code(404);
            require_once __DIR__ . '/../404.php';
            die();
        }
    }

    /**
     * Obtiene un único registro mediante una consulta SELECT.
     *
     * @param array $params Parámetros de la consulta preparada.
     * @return array|false
     */
    public function get_record($params = [])
    {
        try {
            $stmt = $this->create_connection()->prepare($this->query);
            $stmt->execute($params);

            $record = $stmt->fetch();

            $this->close_connection();

            return $record;
        } catch (PDOException $e) {
            http_response_code(404);
            require_once __DIR__ . '/../404.php';
            die();
        }
    }
}
