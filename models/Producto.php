<?php

class Producto {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // Obtener todos los productos para armar el listado
    public function obtenerTodos(): array {
        $query = "SELECT * FROM productos ORDER BY nombre ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insertar un nuevo producto
    public function crear(array $datos): bool {
        $query = "INSERT INTO productos (nombre, descripcion, precio) 
                  VALUES (:nombre, :descripcion, :precio)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'precio' => $datos['precio']
        ]);
    }

    // Obtener los datos de un solo producto por su ID
    public function obtenerPorId(int $id) {
        $query = "SELECT * FROM productos WHERE id_producto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un producto existente
    public function actualizar(int $id, array $datos): bool {
        $query = "UPDATE productos 
                  SET nombre = :nombre, descripcion = :descripcion, precio = :precio 
                  WHERE id_producto = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'precio' => $datos['precio'],
            'id' => $id
        ]);
    }

    // Cambiar estado (activo/inactivo) en lugar de borrarlo para no romper facturas antiguas
    public function cambiarEstado(int $id, string $estado): bool {
        $query = "UPDATE productos SET estado = :estado WHERE id_producto = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'estado' => $estado,
            'id' => $id
        ]);
    }
}
?>