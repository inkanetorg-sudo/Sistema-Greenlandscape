<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

class ProductoController {
    private PDO $db;
    private Producto $productoModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productoModel = new Producto($this->db);
    }

    // Carga la vista con la tabla de productos
    public function index() {
        $productos = $this->productoModel->obtenerTodos();
        require_once __DIR__ . '/../views/admin/productos.php';
    }

    // Procesa el formulario para crear o editar
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => $_POST['precio'] ?? 0
            ];

            if (!empty($_POST['id_producto'])) {
                // Es una edición
                $this->productoModel->actualizar((int)$_POST['id_producto'], $datos);
            } else {
                // Es uno nuevo
                $this->productoModel->crear($datos);
            }
            
            // Redirige de vuelta al listado
            header('Location: /greenland/index.php?action=productos');
            exit;
        }
    }

    // API para cambiar el estado vía AJAX (Activar/Inactivar)
    public function cambiarEstado() {
        $data = json_decode(file_get_contents("php://input"));
        
        if ($data !== null && isset($data->id_producto) && isset($data->estado)) {
            $exito = $this->productoModel->cambiarEstado($data->id_producto, $data->estado);
            header('Content-Type: application/json');
            echo json_encode(['success' => $exito]);
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
}
?>