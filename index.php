<?php
// index.php - Front Controller

// 1. Iniciamos la sesión antes de cualquier salida HTML
session_start();

// 2. Requerimos los controladores
// Cambiamos RutaController por EmpleadoController
require_once __DIR__ . '/controllers/EmpleadoController.php'; 
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/LoginController.php';
require_once __DIR__ . '/controllers/ClienteController.php';
require_once __DIR__ . '/controllers/UsuarioController.php';
require_once __DIR__ . '/controllers/ServicioController.php';
require_once __DIR__ . '/config/database.php';

// 3. Capturamos la acción (por defecto al login si no hay acción)
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// --- PROTECCIÓN DE RUTAS ---
if (!isset($_SESSION['id_usuario']) && $action !== 'login' && $action !== 'procesar_login') {
    header('Location: /greenland/index.php?action=login');
    exit;
}
// ----------------------------------

// 4. Evaluamos la acción
switch ($action) {
    // --- RUTAS PÚBLICAS ---
    case 'login':
        $controller = new LoginController();
        $controller->mostrarLogin();
        break;

    case 'procesar_login':
        $controller = new LoginController();
        $controller->procesar();
        break;
        
    case 'logout':
        session_destroy();
        header('Location: /greenland/index.php?action=login');
        break;

    // --- RUTAS DEL EMPLEADO ---
    case 'mi_ruta':
        if ($_SESSION['rol'] !== 'empleado') { die("Acceso denegado."); }
        $controller = new EmpleadoController();
        $controller->miRuta();
        break;

    case 'completar_visita':
        if ($_SESSION['rol'] !== 'empleado') { die("Acceso denegado."); }
        $controller = new EmpleadoController();
        $controller->completarVisita();
        break;

    // --- RUTAS DEL ADMINISTRADOR ---
    case 'dashboard':
    case 'api_rutas_hoy':
    case 'crear_ruta':
    case 'guardar_ruta':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado. Área exclusiva de administración."); }
        
        $adminController = new AdminController();
        if ($action === 'dashboard') $adminController->dashboard();
        if ($action === 'api_rutas_hoy') $adminController->obtenerRutasHoy();
        if ($action === 'crear_ruta') $adminController->creadorDeRutas();
        if ($action === 'guardar_ruta') $adminController->guardarRuta();
        break;
    
    case 'clientes':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ClienteController();
        $controller->listar();
        break;
		
	case 'facturar_cliente': // <--- AGREGA ESTO
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ClienteController();
        $controller->generarReporte();
        break;

    case 'cliente_eliminar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ClienteController();
        $controller->eliminar();
        break;
		
	case 'admin_cambiar_estado':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $adminController = new AdminController();
        $adminController->cambiarEstadoRuta();
        break;
    
    // --- RUTAS DE SERVICIOS ---
    case 'servicios':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ServicioController();
        $controller->listar();
        break;

    case 'servicio_crear':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ServicioController();
        $controller->mostrarFormularioCrear();
        break;

    case 'servicio_guardar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ServicioController();
        $controller->guardar();
        break;

    case 'servicio_editar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ServicioController();
        $controller->mostrarFormularioEditar();
        break;

    case 'servicio_actualizar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ServicioController();
        $controller->actualizar();
        break;

    case 'servicio_eliminar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ServicioController();
        $controller->eliminar();
        break;
    
    // --- RUTAS DE EMPLEADOS ---
    case 'empleados':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new UsuarioController();
        $controller->listar();
        break;

    case 'empleado_crear':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new UsuarioController();
        $controller->mostrarFormularioCrear();
        break;

    case 'empleado_guardar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new UsuarioController();
        $controller->guardar();
        break;

    case 'empleado_editar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new UsuarioController();
        $controller->mostrarFormularioEditar();
        break;

    case 'empleado_actualizar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new UsuarioController();
        $controller->actualizar();
        break;

    case 'empleado_eliminar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new UsuarioController();
        $controller->eliminar();
        break;
        
    // --- RUTAS FORMULARIOS CLIENTES ---
    case 'cliente_crear':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ClienteController();
        $controller->mostrarFormularioCrear();
        break;

    case 'cliente_guardar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ClienteController();
        $controller->guardar();
        break;

    case 'cliente_editar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ClienteController();
        $controller->mostrarFormularioEditar();
        break;

    case 'cliente_actualizar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        $controller = new ClienteController();
        $controller->actualizar();
        break;
		
	case 'api_reprogramar_visita':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        // Llamamos al controlador de rutas para que anule la tarea vieja
        require_once __DIR__ . '/controllers/RutaController.php';
        $rutaController = new RutaController();
        $rutaController->reprogramarVisitaAntigua();
        break;
		
	// --- MÓDULO DE PRODUCTOS / CATÁLOGO ---
    case 'productos':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->index();
        break;

    case 'producto_guardar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->guardar();
        break;

    case 'producto_cambiar_estado':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->cambiarEstado();
        break;
    
	// --- MÓDULO DE FACTURACIÓN (INVOICES) ---
    case 'facturas':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/FacturaController.php';
        $controller = new FacturaController();
        $controller->index();
        break;

    case 'facturas_generar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/FacturaController.php';
        $controller = new FacturaController();
        $controller->generarBatch();
        break;
		
	case 'factura_eliminar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/FacturaController.php';
        $controller = new FacturaController();
        $controller->eliminar();
        break;
		
	case 'factura_pdf':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/FacturaController.php';
        $controller = new FacturaController();
        $controller->verPdf();
        break;
		
	case 'factura_pagar':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/FacturaController.php';
        $controller = new FacturaController();
        $controller->registrarPago();
        break;
	
	case 'facturas_enviar_masivo':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/FacturaController.php';
        $controller = new FacturaController();
        $controller->enviarMasivo();
        break;
		
	case 'reportes':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/FacturaController.php';
        $controller = new FacturaController();
        $controller->reporteMensual();
        break;
	
	case 'marketing':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/MarketingController.php';
        $controller = new MarketingController($db); // Asegúrate de pasar tu variable $db
        $controller->index();
        break;

    case 'marketing_aviso_temporada':
        if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
        require_once 'controllers/MarketingController.php';
        $controller = new MarketingController($db);
        $controller->avisoTemporada();
        break;
		
	case 'marketing_segmentado':
		if ($_SESSION['rol'] !== 'admin') { die("Acceso denegado."); }
		require_once 'controllers/MarketingController.php';
		$controller = new MarketingController($db);
		$controller->filtrarClientesPorServicio();
		break;
	
    default:
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        break;
}