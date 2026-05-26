<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Ruta.php';

class EmpleadoController {
    private PDO $db;
    private Ruta $rutaModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->rutaModel = new Ruta($this->db);
    }

    // Muestra la lista y el mapa de casas al empleado según la fecha elegida
    public function miRuta() {
        $id_empleado = $_SESSION['id_usuario'];
        
        // Si viene una fecha por la URL la usa, si no, usa la fecha de hoy
        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
        
        $paradas = $this->rutaModel->obtenerRutaPorFecha($id_empleado, $fecha);
        require_once __DIR__ . '/../views/empleado/mi_ruta.php';
    }

    // Recibe el JSON desde empleado.js y devuelve otro JSON
    public function completarVisita() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Leer el JSON enviado por fetch()
            $datos = json_decode(file_get_contents("php://input"), true);

            if (isset($datos['id_detalle'])) {
                $id_detalle = (int)$datos['id_detalle'];
                
                // 2. Actualizar en la base de datos
                $exito = $this->rutaModel->actualizarEstadoVisita($id_detalle, 'completado');

                // 3. Responder a JavaScript
                if ($exito) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'No se pudo actualizar la BD']);
                }
                exit;
            }
        }
        
        // Si no es POST o no viene el ID
        echo json_encode(['success' => false, 'error' => 'Petición inválida']);
        exit;
    }
}
?>