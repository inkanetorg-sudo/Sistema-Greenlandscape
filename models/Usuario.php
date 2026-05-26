<?php

class Usuario {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // Obtener todos los empleados
    public function obtenerTodos(): array {
        // Filtramos para ver solo a los empleados (o puedes quitar el WHERE si quieres ver a los admins también)
        $query = "SELECT id_usuario, nombre, email, rol, creado_en FROM usuarios WHERE rol = 'empleado' ORDER BY nombre ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    // Obtener un usuario por ID
    public function obtenerPorId(int $id) {
        $query = "SELECT id_usuario, nombre, email, rol FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Crear un nuevo usuario con la contraseña encriptada
    public function crear(array $datos): bool {
        $query = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'email' => $datos['email'],
            'password' => $datos['password'], // Aquí recibiremos la clave ya hasheada desde el controlador
            'rol' => $datos['rol']
        ]);
    }

    // Actualizar un usuario existente
    public function actualizar(int $id, array $datos): bool {
        // Si el controlador nos envía una nueva contraseña, la actualizamos. Si no, la dejamos igual.
        if (!empty($datos['password'])) {
            $query = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password, rol = :rol WHERE id_usuario = :id";
            $params = [
                'nombre' => $datos['nombre'],
                'email' => $datos['email'],
                'password' => $datos['password'],
                'rol' => $datos['rol'],
                'id' => $id
            ];
        } else {
            $query = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id_usuario = :id";
            $params = [
                'nombre' => $datos['nombre'],
                'email' => $datos['email'],
                'rol' => $datos['rol'],
                'id' => $id
            ];
        }
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    // Eliminar un usuario
    public function eliminar(int $id): bool {
        $query = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}
?>