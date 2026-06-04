<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Ruta.php'; // <-- Movido al inicio para uso global en la clase

class AdminController {
    private PDO $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Carga la pantalla para crear las rutas visualmente
    public function creadorDeRutas() {
        // 1. Obtener clientes
        $stmtClientes = $this->db->query("SELECT id_cliente, nombre_completo, direccion, latitud, longitud FROM clientes WHERE estado = 'activo'");
        $clientes = $stmtClientes->fetchAll();

        // 2. Obtener empleados
        $stmtEmpleados = $this->db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'empleado'");
        $empleados = $stmtEmpleados->fetchAll();

        // 3. Obtener los servicios disponibles
        $stmtServicios = $this->db->query("SELECT id_servicio, nombre_servicio FROM servicios ORDER BY nombre_servicio ASC");
        $servicios = $stmtServicios->fetchAll();

        // 4. Obtener las tareas rezagadas (pendientes de días pasados)
        $rutaModel = new Ruta($this->db);
        $rezagados = $rutaModel->obtenerRezagados();

        require_once __DIR__ . '/../views/admin/crear_ruta.php';
    }

    // Recibe la ruta armada desde el mapa vía Fetch API
    public function guardarRuta() {
        $data = json_decode(file_get_contents("php://input"));

        // Validación estricta para evitar Fatal Errors si el JSON llega roto
        if ($data === null) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Formato de datos JSON inválido']);
            exit;
        }

        if (!empty($data->id_empleado) && !empty($data->fecha) && !empty($data->paradas)) {
            try {
                $this->db->beginTransaction();

                // 1. Insertar la cabecera de la ruta
                $stmtRuta = $this->db->prepare("INSERT INTO rutas (id_empleado, fecha_ruta) VALUES (?, ?)");
                $stmtRuta->execute([$data->id_empleado, $data->fecha]);
                $id_ruta = $this->db->lastInsertId();

                // 2. Preparar la inserción de las nuevas paradas
                $stmtDetalle = $this->db->prepare("INSERT INTO ruta_detalles (id_ruta, id_cliente, id_servicio, orden_visita) VALUES (?, ?, ?, ?)");
                
                // 3. Preparar la actualización para anular los rezagados
                $stmtActualizarRezagado = $this->db->prepare("UPDATE ruta_detalles SET estado_visita = 'reprogramado' WHERE id_detalle = ?");
                
                $orden = 1;
                foreach ($data->paradas as $parada) {
                    $stmtDetalle->execute([$id_ruta, $parada->id_cliente, $parada->id_servicio, $orden]);
                    
                    if (isset($parada->id_detalle_antiguo) && !empty($parada->id_detalle_antiguo)) {
                        $stmtActualizarRezagado->execute([$parada->id_detalle_antiguo]);
                    }

                    $orden++;
                }

                $this->db->commit();
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $this->db->rollBack();
                
                // Limpieza de buffer para que el error de MySQL no rompa el JSON
                if (ob_get_length()) ob_clean(); 
                
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        }
    }
    
    // Carga la vista principal del administrador
    public function dashboard() {
        $stmt = $this->db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'empleado' ORDER BY nombre ASC");
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    // Devuelve el JSON de las paradas filtradas para el mapa
    public function obtenerRutasHoy() {
        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
        $id_empleado = isset($_GET['empleado']) ? $_GET['empleado'] : 'todos';

        $query = "
            SELECT rd.id_detalle, r.id_ruta, u.nombre AS nombre_jardinero, 
                   c.nombre_completo AS nombre_cliente, c.latitud, c.longitud, 
                   rd.estado_visita, rd.orden_visita, s.nombre_servicio, s.precio
            FROM rutas r
            JOIN usuarios u ON r.id_empleado = u.id_usuario
            JOIN ruta_detalles rd ON r.id_ruta = rd.id_ruta
            JOIN clientes c ON rd.id_cliente = c.id_cliente
            JOIN servicios s ON rd.id_servicio = s.id_servicio
            WHERE r.fecha_ruta = :fecha
        ";

        $params = ['fecha' => $fecha];

        if ($id_empleado !== 'todos') {
            $query .= " AND r.id_empleado = :id_empleado";
            $params['id_empleado'] = $id_empleado;
        }

        $query .= " ORDER BY r.id_ruta, rd.orden_visita ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($resultados);
        exit;
    }
    
    // Recibe JSON desde el modal del dashboard para forzar el cambio de estado
    public function cambiarEstadoRuta() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = json_decode(file_get_contents("php://input"), true);
            
            if ($datos !== null && isset($datos['id_detalle']) && isset($datos['estado'])) {
                $rutaModel = new Ruta($this->db);
                $exito = $rutaModel->actualizarEstadoVisita((int)$datos['id_detalle'], $datos['estado']);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => $exito]);
                exit;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
}