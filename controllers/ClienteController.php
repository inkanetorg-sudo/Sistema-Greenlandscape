<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private PDO $db;
    private Cliente $clienteModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->clienteModel = new Cliente($this->db);
    }

    // Muestra la tabla con todos los clientes
    public function listar() {
        $clientes = $this->clienteModel->obtenerTodos();
        require_once __DIR__ . '/../views/admin/clientes_listado.php';
    }

    // Elimina un cliente y recarga la página
    public function eliminar() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $this->clienteModel->eliminar($id);
        }
        // Redirigimos de vuelta a la lista
        header('Location: /greenland/index.php?action=clientes');
        exit;
    }
	
	// Mostrar formulario vacío
    public function mostrarFormularioCrear() {
        $cliente = null; // Como es nuevo, no hay datos previos
        require_once __DIR__ . '/../views/admin/cliente_form.php';
    }

    // Procesar el guardado del nuevo cliente
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre_completo' => $_POST['nombre_completo'],
                'direccion' => $_POST['direccion'],
                'telefono' => $_POST['telefono'],
                'latitud' => $_POST['latitud'],
                'longitud' => $_POST['longitud']
            ];
            $this->clienteModel->crear($datos);
            header('Location: /greenland/index.php?action=clientes');
            exit;
        }
    }

    // Mostrar formulario con datos cargados
    public function mostrarFormularioEditar() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $cliente = $this->clienteModel->obtenerPorId($id);
            require_once __DIR__ . '/../views/admin/cliente_form.php';
        }
    }

    // Procesar la actualización
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cliente'])) {
            $id = (int)$_POST['id_cliente'];
            $datos = [
                'nombre_completo' => $_POST['nombre_completo'],
                'direccion' => $_POST['direccion'],
                'telefono' => $_POST['telefono'],
                'latitud' => $_POST['latitud'],
                'longitud' => $_POST['longitud']
            ];
            $this->clienteModel->actualizar($id, $datos);
            header('Location: /greenland/index.php?action=clientes');
            exit;
        }
    }
	
	public function generarReporte() {
		$id_cliente = (int)$_GET['id'];
		$inicio = $_GET['inicio'] ?? date('Y-m-01'); // Por defecto al 1ro del mes
		$fin = $_GET['fin'] ?? date('Y-m-d');
		
		$reporte = $this->clienteModel->obtenerReporteServicios($id_cliente, $inicio, $fin);
		require_once __DIR__ . '/../views/admin/reporte_facturacion.php';
	}
}
?>