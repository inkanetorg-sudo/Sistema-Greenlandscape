<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Servicio.php';

class ServicioController {
    private PDO $db;
    private Servicio $servicioModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->servicioModel = new Servicio($this->db);
    }

    public function listar() {
        $servicios = $this->servicioModel->obtenerTodos();
        require_once __DIR__ . '/../views/admin/servicios_listado.php';
    }

    public function mostrarFormularioCrear() {
        $servicio = null;
        require_once __DIR__ . '/../views/admin/servicio_form.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre_servicio' => trim($_POST['nombre_servicio']),
                'descripcion' => trim($_POST['descripcion']),
                'duracion_estimada' => (int)$_POST['duracion_estimada'],
                'precio' => (float)$_POST['precio'] // <-- NUEVA LÍNEA
            ];
			
            $this->servicioModel->crear($datos);
            header('Location: /greenland/index.php?action=servicios');
            exit;
        }
    }

    public function mostrarFormularioEditar() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $servicio = $this->servicioModel->obtenerPorId($id);
            require_once __DIR__ . '/../views/admin/servicio_form.php';
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_servicio'])) {
            $id = (int)$_POST['id_servicio'];
            $datos = [
                'nombre_servicio' => trim($_POST['nombre_servicio']),
                'descripcion' => trim($_POST['descripcion']),
                'duracion_estimada' => (int)$_POST['duracion_estimada'],
                'precio' => (float)$_POST['precio'] // <-- ¡Esto faltaba aquí!
            ];
            $this->servicioModel->actualizar($id, $datos);
            header('Location: /greenland/index.php?action=servicios');
            exit;
        }
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            try {
                $this->servicioModel->eliminar($id);
            } catch (Exception $e) {
                // Si hay un error (ej. restricción de llave foránea), lo ideal sería mostrar un mensaje.
                // Por ahora solo redirigimos para que no rompa la aplicación.
            }
        }
        header('Location: /greenland/index.php?action=servicios');
        exit;
    }
}
?>