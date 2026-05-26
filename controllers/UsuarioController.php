<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private PDO $db;
    private Usuario $usuarioModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioModel = new Usuario($this->db);
    }

    public function listar() {
        $empleados = $this->usuarioModel->obtenerTodos();
        require_once __DIR__ . '/../views/admin/empleados_listado.php';
    }

    public function mostrarFormularioCrear() {
        $empleado = null;
        require_once __DIR__ . '/../views/admin/empleado_form.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Encriptamos la contraseña con BCRYPT (el estándar de PHP)
            $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $datos = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => $passwordHash,
                'rol' => 'empleado' // Forzamos el rol para evitar que creen admins accidentalmente
            ];
            
            $this->usuarioModel->crear($datos);
            header('Location: /greenland/index.php?action=empleados');
            exit;
        }
    }

    public function mostrarFormularioEditar() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $empleado = $this->usuarioModel->obtenerPorId($id);
            require_once __DIR__ . '/../views/admin/empleado_form.php';
        }
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
            $id = (int)$_POST['id_usuario'];
            
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'rol' => 'empleado',
                'password' => '' // Por defecto vacío
            ];

            // Si el administrador escribió algo en el campo de contraseña, la actualizamos
            if (!empty($_POST['password'])) {
                $datos['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            $this->usuarioModel->actualizar($id, $datos);
            header('Location: /greenland/index.php?action=empleados');
            exit;
        }
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $this->usuarioModel->eliminar($id);
        }
        header('Location: /greenland/index.php?action=empleados');
        exit;
    }
}
?>