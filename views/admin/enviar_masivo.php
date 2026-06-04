<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Envío Masivo de Invoices</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        .content-container { padding: 2rem; max-width: 1000px; margin: auto; }
        
        .header-section { margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; }
        
        /* Estilos de la tabla similares a tu catálogo */
        .table-container { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151; font-weight: 600; }
        td { padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
        tr:hover { background-color: #f9fafb; }
        
        .btn-action { padding: 0.5rem 1rem; background-color: #6366f1; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85rem; font-weight: 500; }
        .btn-action:hover { background-color: #4f46e5; }
        .btn-back { padding: 0.5rem 1rem; background-color: #9ca3af; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="content-container">
        <div class="header-section">
            <h1 style="margin: 0; color: #1f2937;">📧 Envío Masivo de Invoices</h1>
            <button class="btn-back" onclick="window.history.back()">⬅️ Volver</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($facturas)): ?>
                        <tr><td colspan="3" style="text-align: center; padding: 2rem;">No hay facturas pendientes de envío.</td></tr>
                    <?php else: ?>
                        <?php foreach($facturas as $f): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($f['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($f['email'] ?? 'Sin correo'); ?></td>
                                <td>
                                    <?php if(!empty($f['email'])): ?>
                                        <a href="mailto:<?php echo $f['email']; ?>?subject=Invoice Green Village - INV-<?php echo str_pad($f['id_factura'], 5, '0', STR_PAD_LEFT); ?>&body=Hola <?php echo htmlspecialchars($f['nombre_completo']); ?>,%0A%0AAdjunto encontrará su factura por valor de $<?php echo number_format($f['total'], 2); ?>.%0A%0APor favor, acceda al sistema para visualizar el detalle y realizar su pago.%0A%0AGreen Village Landscape" 
                                           class="btn-action">📧 Enviar Email</a>
                                    <?php else: ?>
                                        <span style="color: #991b1b; font-size: 0.8rem;">Falta email</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>