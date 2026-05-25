<?php

class Conexion {
    private static $instancia = null;

  
    public static function conectar() {
        if (self::$instancia === null) {
            try {
                // Ruta del archivo .env (sube un nivel desde config/)
                $rutaEnv = dirname(__DIR__) . '/.env';
                
                // Parsear el archivo .env manualmente si existe
                if (file_exists($rutaEnv)) {
                    $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lineas as $linea) {
                        // Ignorar comentarios en el archivo .env
                        if (strpos(trim($linea), '#') === 0) continue;
                        
                        // Separar por el signo de igual (=)
                        list($nombre, $valor) = explode('=', $linea, 2);
                        $_ENV[trim($nombre)] = trim($valor);
                    }
                }

                // Asignamos variables extraídas o usar valores por defecto
                $host     = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'localhost';
                $db_name  = isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'bd_inventario_ventas';
                $usuario  = isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root';
                $password = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '';

                // Esto es para configurar opciones de optimización y seguridad para PDO
                $opciones = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones si hay errores SQL
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,       // Devuelve los datos como objetos limpios
                    PDO::ATTR_EMULATE_PREPARES   => false,                // Desactiva la emulación para prevenir Inyección SQL
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"   // Forzar codificación UTF-8 para tildes y eñes
                ];

                //Crear la conexión PDO
                self::$instancia = new PDO(
                    "mysql:host={$host};dbname={$db_name};charset=utf8mb4", 
                    $usuario, 
                    $password, 
                    $opciones
                );

            } catch (PDOException $e) {
                // Detener la aplicación si la base de datos falla
                die("Error crítico en la conexión de Tecnobyte: " . $e->getMessage());
            }
        }
        
        return self::$instancia;
    }
}