<div class="card" style="margin-top: 2rem;">
    <h2>📜 Historial de Estimaciones</h2>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $historial = $this->db->prepare("SELECT * FROM estimaciones WHERE id_cliente = ? ORDER BY fecha_envio DESC");
            $historial->execute([$id_cliente]);
            foreach($historial as $e): ?>
            <tr>
                <td><?php echo $e['fecha_envio']; ?></td>
                <td>$<?php echo number_format($e['total_estimado'], 2); ?></td>
                <td><a href="/greenland/pdf/<?php echo $e['archivo_pdf']; ?>" target="_blank">📄 Ver PDF</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Botón para generar nueva -->
    <a href="/greenland/index.php?action=nueva_estimacion&id_cliente=<?php echo $id_cliente; ?>" 
       class="btn-marketing" style="display:inline-block; margin-top:1rem;">+ Nueva Estimación</a>
</div>