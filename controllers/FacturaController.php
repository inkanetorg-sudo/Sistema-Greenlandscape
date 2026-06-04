<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Factura.php';

class FacturaController {
    private PDO $db;
    private Factura $facturaModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->facturaModel = new Factura($this->db);
    }

    // Muestra la vista principal con la tabla de facturas
    public function index() {
        $facturas = $this->facturaModel->obtenerTodas();
        require_once __DIR__ . '/../views/admin/facturas.php';
    }

    // Procesa la generación masiva de facturas de un mes
    public function generarBatch() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibimos el mes y el año del formulario
            $mes = isset($_POST['mes']) ? (int)$_POST['mes'] : date('n');
            $anio = isset($_POST['anio']) ? (int)$_POST['anio'] : date('Y');

            // Llamamos a la función mágica que agrupa las rutas del mes
            $resultado = $this->facturaModel->generarFacturasDelMes($anio, $mes);
            
            // Opcional: Podrías usar sesiones aquí para mostrar si hubo error o éxito
            // pero por ahora redirigimos directamente a la tabla para ver el resultado.
            header('Location: /greenland/index.php?action=facturas');
            exit;
        }
    }
	
	// Recibe la petición AJAX para borrar una factura
    public function eliminar() {
        $data = json_decode(file_get_contents("php://input"));
        
        if ($data !== null && isset($data->id_factura)) {
            $exito = $this->facturaModel->eliminar((int)$data->id_factura);
            header('Content-Type: application/json');
            echo json_encode(['success' => $exito]);
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit;
    }
	
	// Genera y muestra la vista optimizada para imprimir el PDF
    public function verPdf() {
        $id_factura = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id_factura > 0) {
            $factura = $this->facturaModel->obtenerPorId($id_factura);
            $detalles = $this->facturaModel->obtenerDetalles($id_factura);
            
            if ($factura) {
                require_once __DIR__ . '/../views/admin/factura_pdf.php';
                exit;
            }
        }
        
        die("Error: La factura solicitada no existe.");
    }
	
	// Procesa el formulario de registro de pago
    public function registrarPago() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_factura = isset($_POST['id_factura']) ? (int)$_POST['id_factura'] : 0;
            $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : 0;
            $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';
            $fecha_pago = isset($_POST['fecha_pago']) ? $_POST['fecha_pago'] : date('Y-m-d');

            if ($id_factura > 0 && $monto > 0 && !empty($metodo_pago)) {
                $this->facturaModel->registrarPago($id_factura, $monto, $metodo_pago, $fecha_pago);
            }
            
            // Recargamos la tabla para ver el cambio a verde
            header('Location: /greenland/index.php?action=facturas');
            exit;
        }
    }
	
	public function enviarMasivo() {
        // Obtenemos las facturas abiertas que están marcadas para enviar por correo
        $query = "SELECT f.id_factura, f.total, c.nombre_completo, c.email 
                  FROM facturas f 
                  JOIN clientes c ON f.id_cliente = c.id_cliente 
                  WHERE f.estado = 'open' AND f.para_correo = 1";
        
        $facturas = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);

        // Cargamos la vista de envío
        require_once __DIR__ . '/../views/admin/enviar_masivo.php';
        exit;
    }
	
	public function reporteMensual() {
        // Obtenemos los totales agrupados por estado
        $query = "SELECT estado, COUNT(*) as cantidad, SUM(total) as monto_total 
                  FROM facturas 
                  GROUP BY estado";
        $reporte = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtenemos los ingresos reales (pagos registrados)
        $queryPagos = "SELECT SUM(monto) as ingresado FROM pagos";
        $ingresos = $this->db->query($queryPagos)->fetch(PDO::FETCH_ASSOC);
        
        require_once __DIR__ . '/../views/admin/reportes.php';
    }
	
	public function nuevaEstimacion($id_cliente) {
		// 1. Crear el registro vacío en la base de datos
		$stmt = $this->db->prepare("INSERT INTO estimaciones (id_cliente, total_estimado) VALUES (?, 0)");
		$stmt->execute([$id_cliente]);
		
		$id_estimacion = $this->db->lastInsertId();
		
		// 2. Redirigir al generador de PDF (usando la misma lógica de tus Invoices)
		header("Location: /greenland/index.php?action=estimacion_pdf&id=$id_estimacion");
		exit;
	}	
}
?>