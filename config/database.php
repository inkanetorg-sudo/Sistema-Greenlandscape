<?php

class Database {
    // Credenciales (Recuerda poner las de tu hosting cuando lo subas)
    private $host = "localhost";
    private $db_name = "sistema_rutas";
    private $username = "root"; 
    private $password = "";     
    public $conn;

    // Método para obtener la conexión a la base de datos
    public function getConnection() {
        $this->conn = null;

        try {
            // Configuración del DSN (Data Source Name)
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // Opciones de PDO para un mejor manejo de errores y seguridad
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos por defecto
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Evita inyección SQL forzando tipos nativos
            ];

            // Instancia de PDO
            $this->conn = new PDO($dsn, $this->username, $this->password, $opciones);

        } catch(PDOException $exception) {
            // 1. Guardamos el error real en el archivo de registro del servidor (Nadie más lo ve)
            error_log("Error crítico de BD: " . $exception->getMessage());
            
            // 2. Le mostramos a la aplicación un mensaje genérico y seguro
            die("Error del sistema: No se pudo conectar a la base de datos. Por favor, intente más tarde.");
        }

        return $this->conn;
    }
}

$database = new Database();
$db = $database->getConnection();

?>