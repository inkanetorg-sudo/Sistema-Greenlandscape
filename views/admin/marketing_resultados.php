<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de Segmentación</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        .content-container { padding: 2rem; max-width: 1000px; margin: auto; }
        .table-container { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151; font-weight: 600; }
        td { padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
        tr:hover { background-color: #f9fafb; }
        
        .btn-action { background-color: #059669; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-size: 0.85rem; font-weight: bold; }
        .btn-action:hover { background-color: #047857; }
        .btn-back { padding: 0.5rem 1rem; background-color: #9ca3af; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="content-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="margin: 0; color: #1f2937;">🎯 Clientes para Ofrecer Servicio</h1>
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
                    <?php if(empty($clientesFiltrados)): ?>
                        <tr><td colspan="3" style="text-align: center; padding: 2rem;">Todos los clientes ya tienen este servicio o no hay resultados.</td></tr>
                    <?php else: ?>
                        <?php foreach($clientesFiltrados as $c): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo htmlspecialchars($c['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($c['email'] ?? 'Sin correo'); ?></td>
                                <td>
                                    <?php if(!empty($c['email'])): ?>
                                        <a href="mailto:<?php echo $c['email']; ?>?subject=Oferta Especial de Temporada - Green Village Landscape&body=Hola <?php echo htmlspecialchars($c['nombre_completo']); ?>,%0A%0AHemos notado que aún no cuentas con este servicio. ¡Nos encantaría ayudarte a mejorar tu jardín esta temporada!%0A%0AContáctanos para darte un presupuesto sin compromiso.%0A%0AGreen Village Landscape" 
                                           class="btn-action">📧 Enviar Oferta</a>
                                    <?php else: ?>
                                        <span style="color: #991b1b; font-size: 0.8rem;">Falta email</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>