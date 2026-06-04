<?php

class Factura {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function generarFacturasDelMes(int $anio, int $mes): array {
        $fecha_inicio = "$anio-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01";
        $fecha_fin = date("Y-m-t", strtotime($fecha_inicio)); 
        
        $fecha_emision = date('Y-m-d'); 
        $mes_siguiente = $mes == 12 ? 1 : $mes + 1;
        $anio_siguiente = $mes == 12 ? $anio + 1 : $anio;
        $fecha_vencimiento = "$anio_siguiente-" . str_pad($mes_siguiente, 2, '0', STR_PAD_LEFT) . "-15";

        // NUEVO: Generar el texto del período (Ej: "Mayo 2026")
        $nombres_meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $texto_periodo = $nombres_meses[$mes] . ' ' . $anio;

        $query = "
            SELECT rd.id_cliente, rd.id_servicio, s.nombre_servicio, s.precio, COUNT(rd.id_detalle) as cantidad
            FROM ruta_detalles rd
            JOIN rutas r ON rd.id_ruta = r.id_ruta
            JOIN servicios s ON rd.id_servicio = s.id_servicio
            WHERE r.fecha_ruta BETWEEN :inicio AND :fin 
            AND rd.estado_visita = 'completado'
            GROUP BY rd.id_cliente, rd.id_servicio, s.nombre_servicio, s.precio
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['inicio' => $fecha_inicio, 'fin' => $fecha_fin]);
        $servicios_realizados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($servicios_realizados)) {
            return ['success' => false, 'error' => 'No hay servicios completados en este mes para facturar.'];
        }

        $clientes_a_facturar = [];
        foreach ($servicios_realizados as $fila) {
            $id_cliente = $fila['id_cliente'];
            if (!isset($clientes_a_facturar[$id_cliente])) {
                $clientes_a_facturar[$id_cliente] = ['total' => 0, 'servicios' => []];
            }
            $subtotal = $fila['precio'] * $fila['cantidad'];
            $clientes_a_facturar[$id_cliente]['total'] += $subtotal;
            $clientes_a_facturar[$id_cliente]['servicios'][] = [
                'id_servicio' => $fila['id_servicio'],
                'nombre' => $fila['nombre_servicio'],
                'cantidad' => $fila['cantidad'],
                'precio_unitario' => $fila['precio'],
                'subtotal' => $subtotal
            ];
        }

        try {
            $this->conn->beginTransaction();
            $facturas_generadas = 0;

            // ACTUALIZADO: Añadimos 'periodo' a la consulta SQL
            $stmtFactura = $this->conn->prepare("INSERT INTO facturas (id_cliente, periodo, fecha_emision, fecha_vencimiento, estado, total, para_imprimir, para_correo) VALUES (?, ?, ?, ?, 'open', ?, 1, 1)");
            $stmtDetalle = $this->conn->prepare("INSERT INTO factura_detalles (id_factura, id_producto, descripcion_custom, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?, ?)");

            foreach ($clientes_a_facturar as $id_cliente => $datos) {
                // ACTUALIZADO: Pasamos el $texto_periodo a la ejecución
                $stmtFactura->execute([$id_cliente, $texto_periodo, $fecha_emision, $fecha_vencimiento, $datos['total']]);
                $id_factura = $this->conn->lastInsertId();

                foreach ($datos['servicios'] as $srv) {
                    $stmtDetalle->execute([
                        $id_factura, 
                        $srv['id_servicio'], 
                        $srv['nombre'], 
                        $srv['cantidad'], 
                        $srv['precio_unitario'], 
                        $srv['subtotal']
                    ]);
                }
                $facturas_generadas++;
            }

            $this->conn->commit();
            return ['success' => true, 'mensaje' => "Se generaron exitosamente $facturas_generadas facturas."];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ACTUALIZADO: Traemos la columna f.periodo
    public function obtenerTodas() {
        $query = "
            SELECT f.id_factura, c.nombre_completo, f.periodo, f.fecha_emision, f.fecha_vencimiento, f.total, f.estado 
            FROM facturas f
            JOIN clientes c ON f.id_cliente = c.id_cliente
            ORDER BY f.id_factura DESC
        ";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar(int $id_factura): bool {
        $query = "DELETE FROM facturas WHERE id_factura = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute(['id' => $id_factura]);
    }
	
	// Obtener la cabecera completa de una factura específica
    public function obtenerPorId(int $id_factura) {
        $query = "SELECT f.*, c.nombre_completo, c.direccion, c.telefono 
                  FROM facturas f 
                  JOIN clientes c ON f.id_cliente = c.id_cliente 
                  WHERE f.id_factura = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id_factura]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener la lista de servicios (detalle) de una factura específica
    public function obtenerDetalles(int $id_factura) {
        $query = "SELECT * FROM factura_detalles WHERE id_factura = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id' => $id_factura]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
	// Registra el pago y actualiza el estado de la factura
    public function registrarPago(int $id_factura, float $monto, string $metodo_pago, string $fecha_pago): bool {
        try {
            $this->conn->beginTransaction();

            // 1. Guardar el registro del pago
            $queryPago = "INSERT INTO pagos (id_factura, monto, metodo_pago, fecha_pago) VALUES (?, ?, ?, ?)";
            $stmtPago = $this->conn->prepare($queryPago);
            $stmtPago->execute([$id_factura, $monto, $metodo_pago, $fecha_pago]);

            // 2. Cambiar el estado de la factura a 'paid'
            $queryFactura = "UPDATE facturas SET estado = 'paid' WHERE id_factura = ?";
            $stmtFactura = $this->conn->prepare($queryFactura);
            $stmtFactura->execute([$id_factura]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>