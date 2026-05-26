<?php
require_once __DIR__ . '/../config/database.php';

class AdminController {
    private PDO $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Carga la pantalla para crear las rutas visualmente
    public function creadorDeRutas() {
        // 1. Obtener clientes
        $stmtClientes = $this->db->query("SELECT id_cliente, nombre_completo, direccion, latitud, longitud FROM clientes");
        $clientes = $stmtClientes->fetchAll();

        // 2. Obtener empleados
        $stmtEmpleados = $this->db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'empleado'");
        $empleados = $stmtEmpleados->fetchAll();

        // 3. Obtener los servicios disponibles
        $stmtServicios = $this->db->query("SELECT id_servicio, nombre_servicio FROM servicios ORDER BY nombre_servicio ASC");
        $servicios = $stmtServicios->fetchAll();

        // 4. NUEVO: Obtener las tareas rezagadas (pendientes de días pasados)
        require_once __DIR__ . '/../models/Ruta.php';
        $rutaModel = new Ruta($this->db);
        $rezagados = $rutaModel->obtenerRezagados();

        require_once __DIR__ . '/../views/admin/crear_ruta.php';
    }

    // Recibe la ruta armada desde el mapa vía Fetch API
    public function guardarRuta() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->id_empleado) && !empty($data->fecha) && !empty($data->paradas)) {
            try {
                $this->db->beginTransaction();

                // 1. Insertar la cabecera de la ruta
                $stmtRuta = $this->db->prepare("INSERT INTO rutas (id_empleado, fecha_ruta) VALUES (?, ?)");
                $stmtRuta->execute([$data->id_empleado, $data->fecha]);
                $id_ruta = $this->db->lastInsertId();

                // 2. Preparar la inserción de las nuevas paradas
                $stmtDetalle = $this->db->prepare("INSERT INTO ruta_detalles (id_ruta, id_cliente, id_servicio, orden_visita) VALUES (?, ?, ?, ?)");
                
                // 3. NUEVO: Preparar la actualización para anular los rezagados que se están moviendo a hoy
                // (Asumiendo que tu tabla tiene la columna 'id_detalle' y el estado se llama 'reprogramado')
                $stmtActualizarRezagado = $this->db->prepare("UPDATE ruta_detalles SET estado_visita = 'reprogramado' WHERE id_detalle = ?");
                
                $orden = 1;
                foreach ($data->paradas as $parada) {
                    // Insertamos la nueva parada de hoy en la nueva ruta
                    $stmtDetalle->execute([$id_ruta, $parada->id_cliente, $parada->id_servicio, $orden]);
                    
                    // NUEVO: Validamos si esta parada arrastra un rezagado antiguo
                    if (isset($parada->id_detalle_antiguo) && !empty($parada->id_detalle_antiguo)) {
                        $stmtActualizarRezagado->execute([$parada->id_detalle_antiguo]);
                    }

                    $orden++;
                }

                // Si todo salió bien, guardamos los cambios de golpe
                $this->db->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                // Si algo falla, deshace la ruta creada Y devuelve los rezagados a su estado original
                $this->db->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        }
    }
	
	// Carga la vista principal del administrador
    public function dashboard() {
        // Obtenemos los empleados para llenar el filtro desplegable
        $stmt = $this->db->query("SELECT id_usuario, nombre FROM usuarios WHERE rol = 'empleado' ORDER BY nombre ASC");
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    // Devuelve el JSON de las paradas filtradas para el mapa
    public function obtenerRutasHoy() {
        // Captura los filtros de la URL (si no existen, usa valores por defecto)
        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
        $id_empleado = isset($_GET['empleado']) ? $_GET['empleado'] : 'todos';

        // Consulta base
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

        // Si se seleccionó un jardinero en específico, agregamos la condición
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
            
            if (isset($datos['id_detalle']) && isset($datos['estado'])) {
                // Requerimos e instanciamos el modelo de Ruta aquí mismo
                require_once __DIR__ . '/../models/Ruta.php';
                $rutaModel = new Ruta($this->db);
                
                $exito = $rutaModel->actualizarEstadoVisita((int)$datos['id_detalle'], $datos['estado']);
                
                echo json_encode(['success' => $exito]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
}