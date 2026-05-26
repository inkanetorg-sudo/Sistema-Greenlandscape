<?php

class Servicio {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // Obtener todos los servicios disponibles
    public function obtenerTodos(): array {
        $query = "SELECT * FROM servicios ORDER BY nombre_servicio ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    // Obtener un servicio por su ID
    public function obtenerPorId(int $id) {
        $query = "SELECT * FROM servicios WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Registrar un nuevo servicio
    public function crear(array $datos): bool {
        $query = "INSERT INTO servicios (nombre_servicio, descripcion, duracion_estimada, precio) 
                  VALUES (:nombre_servicio, :descripcion, :duracion_estimada, :precio)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'nombre_servicio' => $datos['nombre_servicio'],
            'descripcion' => $datos['descripcion'],
            'duracion_estimada' => $datos['duracion_estimada'],
            'precio' => $datos['precio']
        ]);
    }

    // Actualizar un servicio existente
    public function actualizar(int $id, array $datos): bool {
        $query = "UPDATE servicios 
                  SET nombre_servicio = :nombre_servicio, 
                      descripcion = :descripcion, 
                      duracion_estimada = :duracion_estimada,
                      precio = :precio
                  WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'nombre_servicio' => $datos['nombre_servicio'],
            'descripcion' => $datos['descripcion'],
            'duracion_estimada' => $datos['duracion_estimada'],
            'precio' => $datos['precio'],
            'id' => $id
        ]);
    }

    // Eliminar un servicio
    public function eliminar(int $id): bool {
        $query = "DELETE FROM servicios WHERE id_servicio = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
}
?>