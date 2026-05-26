<?php
// Requerimos la conexión y el modelo
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Ruta.php';

class RutaController {
    private PDO $db;
    private Ruta $rutaModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->rutaModel = new Ruta($this->db);
    }

    // --- MÉTODOS DEL EMPLEADO ---

    // Carga la pantalla principal del jardinero
    public function miRuta(int $id_empleado) {
        // Extraemos las casas que debe visitar hoy
        $paradas = $this->rutaModel->obtenerRutaDelDia($id_empleado);
        
        // Renderizamos la vista
        require_once __DIR__ . '/../views/empleado/mi_ruta.php';
    }
    
    // Este método recibirá el POST en JSON desde el celular del empleado
    public function completarVisita() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (isset($data->id_detalle)) {
            $exito = $this->rutaModel->actualizarEstadoVisita($data->id_detalle, 'completado');
            header('Content-Type: application/json');
            echo json_encode(['success' => $exito]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'ID de detalle no proporcionado']);
        }
    }

    // --- NUEVOS MÉTODOS DEL ADMINISTRADOR ---

    // Carga la pantalla del Creador de Rutas
    public function crearRutaForm() {
        if ($_SESSION['rol'] !== 'admin') die("Acceso denegado.");

        // 1. Aquí debes cargar tus clientes, servicios y empleados como ya lo hacías
        $stmtEmp = $this->db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'empleado'");
        $empleados = $stmtEmp->fetchAll(PDO::FETCH_ASSOC);

        $stmtCli = $this->db->query("SELECT id_cliente, nombre_completo, latitud, longitud FROM clientes");
        $clientes = $stmtCli->fetchAll(PDO::FETCH_ASSOC);

        $stmtServ = $this->db->query("SELECT id_servicio, nombre_servicio FROM servicios");
        $servicios = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

        // 2. Extraemos los rezagados para enviarlos a la vista
        $rezagados = $this->rutaModel->obtenerRezagados();

        require_once __DIR__ . '/../views/admin/crear_ruta.php';
    }

    // Recibe el POST de JS para anular la tarea vieja cuando se reprograma
    public function reprogramarVisitaAntigua() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (isset($data->id_detalle)) {
            // Cambiamos el estado viejo a 'reprogramado' para que ya no salga como pendiente
            $exito = $this->rutaModel->actualizarEstadoVisita($data->id_detalle, 'reprogramado');
            header('Content-Type: application/json');
            echo json_encode(['success' => $exito]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
        }
    }
}
?>