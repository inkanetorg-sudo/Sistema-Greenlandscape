<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturación Mensual - Greenland</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s;}
        .btn-primary:hover { background-color: #2563eb; }
        
        .form-inline { display: flex; gap: 1rem; align-items: center; }
        .form-inline select { padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 1rem;}

        /* Estilos de la Tabla */
        .table-container { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151; }
        td { padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
        tr:hover { background-color: #f9fafb; }
        
        .badge { padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.85rem; font-weight: bold; }
        .badge-open { background-color: #fef08a; color: #854d0e; }
        .badge-paid { background-color: #dcfce7; color: #166534; }
        .badge-past_due { background-color: #fee2e2; color: #991b1b; }
        .badge-void { background-color: #f3f4f6; color: #374151; text-decoration: line-through;}
        
        .btn-action { padding: 0.4rem 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; color: white; text-decoration: none;}
        .btn-view { background-color: #6366f1; }
    </style>
</head>
<body>
    
    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar">
        <div style="padding: 2rem;">
            
            <div class="header-actions">
                <div>
                    <h1 style="margin: 0; color: #1f2937;">Facturación Mensual (Invoices)</h1>
                    <p style="margin: 0; color: #6b7280;">Agrupa todas las rutas completadas del mes en un solo invoice por cliente.</p>
                </div>
                
                <form class="form-inline" action="/greenland/index.php?action=facturas_generar" method="POST" onsubmit="return confirm('¿Estás seguro de generar las facturas para este mes? El sistema sumará automáticamente todas las rutas completadas.');">
                    <select name="mes" required>
                        <?php 
                            $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                            $mesActual = date('n');
                            foreach($meses as $index => $nombreMes): 
                                $numMes = $index + 1;
                                $selected = ($numMes == $mesActual) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $numMes; ?>" <?php echo $selected; ?>><?php echo $nombreMes; ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="anio" required>
                        <?php 
                            $anioActual = date('Y');
                            for($i = $anioActual - 1; $i <= $anioActual + 1; $i++):
                                $selected = ($i == $anioActual) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    
                    <button type="submit" class="btn-primary">⚡ Generar Invoices del Mes</button>
					<button type="button" class="btn-primary" style="background-color: #6366f1;" onclick="enviarFacturasMasivo()">
						📧 Enviar Invoices por Email
					</button>
                </form>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Factura #</th>
                            <th>Cliente</th>
                            <th>Período</th> <!-- NUEVO -->
                            <th>Emisión</th>
                            <th>Vencimiento</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($facturas)): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 2rem;">No hay facturas generadas aún. Genera las del mes en curso arriba.</td></tr>
                        <?php else: ?>
                            <?php foreach($facturas as $fac): ?>
                                <tr>
                                    <td style="font-weight: bold; color: #374151;">INV-<?php echo str_pad($fac['id_factura'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($fac['nombre_completo']); ?></td>
									<td>
                                        <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 12px; font-size: 0.85rem; font-weight: bold;">
                                            📅 <?php echo htmlspecialchars($fac['periodo'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($fac['fecha_emision'])); ?></td>
                                    
                                    <td style="color: <?php echo (strtotime($fac['fecha_vencimiento']) < time() && $fac['estado'] == 'open') ? '#ef4444' : 'inherit'; ?>; font-weight: <?php echo (strtotime($fac['fecha_vencimiento']) < time() && $fac['estado'] == 'open') ? 'bold' : 'normal'; ?>">
                                        <?php echo date('M d, Y', strtotime($fac['fecha_vencimiento'])); ?>
                                    </td>
                                    
                                    <td style="font-weight: bold;">$<?php echo number_format($fac['total'], 2); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $fac['estado']; ?>">
                                            <?php echo strtoupper(str_replace('_', ' ', $fac['estado'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/greenland/index.php?action=factura_pdf&id=<?php echo $fac['id_factura']; ?>" target="_blank" class="btn-action btn-view">📄 Ver PDF</a>
                                        
                                        <?php if($fac['estado'] !== 'paid' && $fac['estado'] !== 'void'): ?>
                                            <button class="btn-action" style="background-color: #22c55e;" onclick="abrirModalPago(<?php echo $fac['id_factura']; ?>, <?php echo $fac['total']; ?>)">💵 Pagar</button>
                                        <?php endif; ?>
                                        
                                        <button class="btn-action" style="background-color: #ef4444;" onclick="borrarFactura(<?php echo $fac['id_factura']; ?>)">🗑️ Borrar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
<script>
        async function borrarFactura(idFactura) {
            if (!confirm('¿Estás seguro de que deseas eliminar esta factura? Esta acción no se puede deshacer.')) {
                return;
            }

            try {
                const response = await fetch('/greenland/index.php?action=factura_eliminar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_factura: idFactura })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Recarga la página para actualizar la tabla
                    window.location.reload();
                } else {
                    alert('Error al borrar la factura: ' + data.error);
                }
            } catch (error) {
                console.error(error);
                alert('Error de conexión al intentar borrar.');
            }
        }
 </script>
 <div id="modalPago" class="modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:2000;">
        <div class="modal-content" style="background:white; padding:2rem; border-radius:8px; width:100%; max-width:400px;">
            <h2 style="margin-top:0; color: #1f2937;">Registrar Pago</h2>
            <form action="/greenland/index.php?action=factura_pagar" method="POST">
                <input type="hidden" id="pago_id_factura" name="id_factura" value="">
                
                <div class="form-group" style="margin-bottom:1rem;">
                    <label style="display:block; font-weight:bold; margin-bottom:0.5rem;">Monto Recibido ($)</label>
                    <input type="number" id="pago_monto" name="monto" step="0.01" required style="width:100%; padding:0.5rem; border:1px solid #d1d5db; border-radius:4px; box-sizing:border-box;">
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label style="display:block; font-weight:bold; margin-bottom:0.5rem;">Método de Pago</label>
                    <select name="metodo_pago" required style="width:100%; padding:0.5rem; border:1px solid #d1d5db; border-radius:4px; box-sizing:border-box;">
                        <option value="Zelle">Zelle</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Venmo">Venmo</option>
                        <option value="CashApp">CashApp</option>
                        <option value="Check">Check (Cheque)</option>
                        <option value="Cash">Cash (Efectivo)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label style="display:block; font-weight:bold; margin-bottom:0.5rem;">Fecha de Pago</label>
                    <input type="date" name="fecha_pago" value="<?php echo date('Y-m-d'); ?>" required style="width:100%; padding:0.5rem; border:1px solid #d1d5db; border-radius:4px; box-sizing:border-box;">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:1rem;">
                    <button type="button" onclick="document.getElementById('modalPago').style.display='none'" style="background:#9ca3af; color:white; padding:0.5rem 1rem; border:none; border-radius:4px; cursor:pointer;">Cancelar</button>
                    <button type="submit" style="background:#22c55e; color:white; padding:0.5rem 1rem; border:none; border-radius:4px; cursor:pointer; font-weight:bold;">💾 Guardar Pago</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Función para abrir la ventanita y precargar los datos del cliente
        function abrirModalPago(idFactura, total) {
            document.getElementById('pago_id_factura').value = idFactura;
            document.getElementById('pago_monto').value = total;
            document.getElementById('modalPago').style.display = 'flex';
        }
		
		function enviarFacturasMasivo() {
			if (!confirm('¿Deseas abrir el gestor de correo para enviar las facturas pendientes?')) return;

			// Aquí obtendremos las facturas que están pendientes de envío
			// El sistema recorrerá la tabla y abrirá 'mailto' para cada cliente con una factura 'open'
			alert('Preparando el envío masivo...');
			
			// Lógica para iterar clientes con facturas pendientes y enviar email
			// Usaremos un formato estándar: cliente, email, link al PDF
			window.location.href = '/greenland/index.php?action=facturas_enviar_masivo';
		}
    </script>
</body>
</html>