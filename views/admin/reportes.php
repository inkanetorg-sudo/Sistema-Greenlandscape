<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Financiero - Greenland</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        .content-with-sidebar { padding: 2rem; }
        
        /* Estructura de cuadrícula para las tarjetas */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        /* Estilo de cada tarjeta */
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 6px solid #3b82f6; /* Borde azul por defecto */
        }
        
        /* Colores dinámicos según el estado */
        .stat-card.success, .stat-card.paid { border-left-color: #10b981; } /* Verde para cobrado */
        .stat-card.warning, .stat-card.open { border-left-color: #f59e0b; } /* Naranja para pendiente */
        .stat-card.danger, .stat-card.past_due { border-left-color: #ef4444; } /* Rojo para vencido */
        
        .stat-card h3 { margin: 0 0 0.5rem 0; font-size: 1rem; color: #6b7280; text-transform: uppercase; font-weight: bold; }
        .stat-card .amount { margin: 0; font-size: 2.5rem; font-weight: bold; color: #1f2937; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar">
        <h1 style="margin-top: 0; color: #1f2937;">📊 Dashboard Financiero</h1>
        <p style="color: #6b7280; margin-top: 0;">Resumen del estado de cuenta actual.</p>
        
        <div class="dashboard-grid">
            <div class="stat-card success">
                <h3>✅ Ingresos Totales (Cobrado)</h3>
                <p class="amount">$<?php echo number_format($ingresos['ingresado'] ?? 0, 2); ?></p>
            </div>
            
            <?php if(!empty($reporte)): ?>
                <?php foreach($reporte as $r): 
                    // Convertimos el estado (open, paid) a minúsculas para aplicar el color correcto en CSS
                    $claseEstado = strtolower($r['estado']);
                    
                    // Elegimos un icono automático según el estado
                    $icono = '💰';
                    if ($claseEstado == 'open') $icono = '⏳';
                    if ($claseEstado == 'paid') $icono = '✅';
                    if ($claseEstado == 'past_due') $icono = '⚠️';
                ?>
                <div class="stat-card <?php echo $claseEstado; ?>">
                    <h3><?php echo $icono . ' ' . strtoupper($r['estado']); ?></h3>
                    <p class="amount">$<?php echo number_format($r['monto_total'], 2); ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
    </div>

</body>
</html>