<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura - <?php echo $reporte[0]['nombre_completo'] ?? 'Cliente'; ?></title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 800px; margin: 2rem auto; padding: 2rem; border: 1px solid #ddd; }
        .header { text-align: center; border-bottom: 2px solid #16a34a; margin-bottom: 2rem; padding-bottom: 1rem; }
        .info-cliente { margin-bottom: 2rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        th, td { padding: 0.75rem; border-bottom: 1px solid #eee; text-align: left; }
        th { background-color: #f9fafb; }
        .total-row { font-size: 1.2rem; background-color: #f3f4f6; }
        .btn-print { background: #16a34a; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Green Village Landscape</h1>
        <p>Reporte de Servicios Realizados</p>
    </div>

    <div class="info-cliente">
        <h3>Cliente: <?php echo htmlspecialchars($reporte[0]['nombre_completo'] ?? 'N/A'); ?></h3>
        <p>Periodo: <?php echo htmlspecialchars($_GET['inicio'] ?? 'N/A'); ?> al <?php echo htmlspecialchars($_GET['fin'] ?? 'N/A'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Servicio</th>
                <th>Estado</th>
                <th>Precio (USD)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0; 
            foreach ($reporte as $fila): 
                $total += $fila['precio']; 
            ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['fecha_ruta']); ?></td>
                <td><?php echo htmlspecialchars($fila['nombre_servicio']); ?></td>
                <td>
                    <span style="text-transform: uppercase; font-size: 0.8rem; font-weight: bold;">
                        <?php echo htmlspecialchars($fila['estado_visita']); ?>
                    </span>
                </td>
                <td>$<?php echo number_format($fila['precio'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align:right;"><b>TOTAL A FACTURAR:</b></td>
                <td><b>$<?php echo number_format($total, 2); ?></b></td>
            </tr>
        </tfoot>
    </table>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">Imprimir / Guardar PDF</button>
        <a href="/greenland/index.php?action=clientes" style="margin-left: 1rem; color: #666;">Volver</a>
    </div>
</div>

</body>
</html>