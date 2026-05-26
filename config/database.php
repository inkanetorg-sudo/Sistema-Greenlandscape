<?php

class Database {
    // Credenciales de tu entorno local
    private $host = "localhost";
    private $db_name = "sistema_rutas";
    private $username = "root"; // Cambia esto si tu usuario local es distinto
    private $password = "";     // Cambia esto si tienes contraseña en tu entorno local
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
            // En producción, es mejor registrar esto en un archivo de log, 
            // pero en local nos sirve verlo en pantalla.
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>