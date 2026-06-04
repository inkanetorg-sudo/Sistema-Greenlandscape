<?php

class Cliente {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }
    
    // Actualiza el estado del cliente (activo/inactivo)
    public function cambiarEstado(int $id_cliente, string $nuevo_estado): bool {
        $query = "UPDATE clientes SET estado = :estado WHERE id_cliente = :id_cliente";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'estado' => $nuevo_estado, 
            'id_cliente' => $id_cliente
        ]);
    }

    // Obtener TODOS los clientes para el directorio (Para poder ver a los inactivos y activarlos de nuevo)
    public function obtenerTodos(): array {
        $query = "SELECT * FROM clientes ORDER BY nombre_completo ASC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insertar un nuevo cliente en la base de datos
    public function crear(array $datos): bool {
        $query = "INSERT INTO clientes (nombre_completo, direccion, latitud, longitud, telefono) 
                  VALUES (:nombre, :direccion, :latitud, :longitud, :telefono)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'nombre' => $datos['nombre_completo'],
            'direccion' => $datos['direccion'],
            'latitud' => $datos['latitud'],
            'longitud' => $datos['longitud'],
            'telefono' => $datos['telefono']
        ]);
    }

    // Obtener los datos de un solo cliente (Sin importar su estado, por si necesitas editar uno cancelado)
    public function obtenerPorId(int $id) {
        $query = "SELECT * FROM clientes WHERE id_cliente = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar los datos de un cliente existente
    public function actualizar(int $id, array $datos): bool {
        $query = "UPDATE clientes 
                  SET nombre_completo = :nombre, direccion = :direccion, 
                      latitud = :latitud, longitud = :longitud, telefono = :telefono 
                  WHERE id_cliente = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'nombre' => $datos['nombre_completo'],
            'direccion' => $datos['direccion'],
            'latitud' => $datos['latitud'],
            'longitud' => $datos['longitud'],
            'telefono' => $datos['telefono'],
            'id' => $id
        ]);
    }

    // Eliminar un cliente del sistema permanentemente
    public function eliminar(int $id): bool {
        $query = "DELETE FROM clientes WHERE id_cliente = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id]);
    }
    
    // Obtiene el historial de servicios para el estado de cuenta
    public function obtenerReporteServicios(int $id_cliente, string $fecha_inicio, string $fecha_fin): array {
        $query = "
            SELECT rd.id_detalle, c.nombre_completo, s.nombre_servicio, s.precio, 
                   r.fecha_ruta, rd.estado_visita
            FROM ruta_detalles rd
            JOIN rutas r ON rd.id_ruta = r.id_ruta
            JOIN clientes c ON rd.id_cliente = c.id_cliente
            JOIN servicios s ON rd.id_servicio = s.id_servicio
            WHERE c.id_cliente = :id_cliente 
            AND r.fecha_ruta BETWEEN :inicio AND :fin
            ORDER BY r.fecha_ruta DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id_cliente' => $id_cliente, 'inicio' => $fecha_inicio, 'fin' => $fecha_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>