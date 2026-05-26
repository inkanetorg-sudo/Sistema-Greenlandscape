<?php
require_once __DIR__ . '/../config/database.php';

class LoginController {
    private PDO $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Muestra el formulario de inicio de sesión
    public function mostrarLogin() {
        if (isset($_SESSION['id_usuario'])) {
            $this->redirigirSegunRol($_SESSION['rol']);
        }
        require_once __DIR__ . '/../views/login.php';
    }

    // Procesa el formulario (POST)
    public function procesar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $stmt = $this->db->prepare("SELECT id_usuario, nombre, rol, password FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch();

            // Lógica de seguridad:
            // 1. Verificamos con password_verify() (Para los nuevos usuarios con clave encriptada)
            // 2. O verificamos con == (Para los usuarios viejos en texto plano)
            if ($usuario && (password_verify($password, $usuario['password']) || $password === $usuario['password'])) {
                
                // MIGRACIÓN AUTOMÁTICA: Si entró usando texto plano, actualizamos su clave a Hash silenciosamente
                if ($password === $usuario['password']) {
                    $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmtMigracion = $this->db->prepare("UPDATE usuarios SET password = :hash WHERE id_usuario = :id");
                    $stmtMigracion->execute([
                        'hash' => $nuevoHash,
                        'id' => $usuario['id_usuario']
                    ]);
                }

                // Guardamos los datos en la sesión
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                $this->redirigirSegunRol($usuario['rol']);
            } else {
                // Credenciales incorrectas
                $error = "Correo o contraseña incorrectos.";
                require_once __DIR__ . '/../views/login.php';
            }
        }
    }

    private function redirigirSegunRol($rol) {
        if ($rol === 'admin') {
            header('Location: /greenland/index.php?action=dashboard');
        } else {
            header('Location: /greenland/index.php?action=mi_ruta');
        }
        exit;
    }
}
?>