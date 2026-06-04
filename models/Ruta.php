<?php

class Ruta {
    // Usamos tipado de PHP 8 para mayor seguridad
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    // Obtiene las paradas del jardinero para una fecha específica
    public function obtenerRutaPorFecha(int $id_empleado, string $fecha): array {
        $query = "
            SELECT rd.id_detalle, c.nombre_completo, c.direccion, c.telefono, c.latitud, c.longitud, 
                   s.nombre_servicio, rd.estado_visita, rd.orden_visita
            FROM ruta_detalles rd
            JOIN rutas r ON rd.id_ruta = r.id_ruta
            JOIN clientes c ON rd.id_cliente = c.id_cliente
            JOIN servicios s ON rd.id_servicio = s.id_servicio
            WHERE r.id_empleado = :id_empleado AND r.fecha_ruta = :fecha
            ORDER BY rd.orden_visita ASC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'id_empleado' => $id_empleado,
            'fecha' => $fecha
        ]);
        
        // Optimización: FETCH_ASSOC reduce a la mitad el consumo de memoria RAM del servidor
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualiza el estado de la visita (Ej: de 'pendiente' a 'completado')
    public function actualizarEstadoVisita(int $id_detalle, string $estado): bool {
        $query = "UPDATE ruta_detalles SET estado_visita = :estado WHERE id_detalle = :id_detalle";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'estado' => $estado, 
            'id_detalle' => $id_detalle
        ]);
    }
    
    // Obtiene las visitas que quedaron pendientes en días pasados
    public function obtenerRezagados(): array {
        $query = "
            SELECT rd.id_detalle, c.nombre_completo, s.nombre_servicio, 
                   r.fecha_ruta, u.nombre AS jardinero_anterior,
                   rd.id_cliente, rd.id_servicio
            FROM ruta_detalles rd
            JOIN rutas r ON rd.id_ruta = r.id_ruta
            JOIN clientes c ON rd.id_cliente = c.id_cliente
            JOIN servicios s ON rd.id_servicio = s.id_servicio
            JOIN usuarios u ON r.id_empleado = u.id_usuario
            WHERE r.fecha_ruta < CURDATE() AND rd.estado_visita = 'pendiente'
            ORDER BY r.fecha_ruta ASC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>