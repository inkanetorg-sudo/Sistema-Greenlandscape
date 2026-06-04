<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos - Greenland</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .btn-primary { background-color: #22c55e; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .btn-primary:hover { background-color: #16a34a; }
        
        /* Estilos de la Tabla */
        .table-container { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151; }
        td { padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
        tr:hover { background-color: #f9fafb; }
        
        .badge { padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; }
        .badge-activo { background-color: #dcfce7; color: #166534; }
        .badge-inactivo { background-color: #fee2e2; color: #991b1b; }
        
        .btn-action { padding: 0.4rem 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem; margin-right: 0.5rem; color: white; }
        .btn-edit { background-color: #3b82f6; }
        .btn-toggle-active { background-color: #ef4444; }
        .btn-toggle-inactive { background-color: #10b981; }

        /* Estilos del Modal */
        .modal { display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 2000; }
        .modal-content { background: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; }
        .btn-cancel { background-color: #9ca3af; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    
    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar">
        <div style="padding: 2rem;">
            
            <div class="header-actions">
                <div>
                    <h1 style="margin: 0; color: #1f2937;">Catálogo de Productos y Servicios</h1>
                    <p style="margin: 0; color: #6b7280;">Gestiona los items que aparecerán en los Invoices.</p>
                </div>
                <button class="btn-primary" onclick="abrirModal()">➕ Nuevo Producto</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($productos)): ?>
                            <tr><td colspan="6" style="text-align: center;">No hay productos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach($productos as $prod): ?>
                                <tr>
                                    <td>#<?php echo $prod['id_producto']; ?></td>
                                    <td style="font-weight: bold;"><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($prod['descripcion']); ?></td>
                                    <td>$<?php echo number_format($prod['precio'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $prod['estado'] === 'activo' ? 'badge-activo' : 'badge-inactivo'; ?>">
                                            <?php echo ucfirst($prod['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-edit" onclick="editarProducto(<?php echo htmlspecialchars(json_encode($prod)); ?>)">✏️ Editar</button>
                                        <button class="btn-action <?php echo $prod['estado'] === 'activo' ? 'btn-toggle-active' : 'btn-toggle-inactive'; ?>" 
                                                onclick="cambiarEstado(<?php echo $prod['id_producto']; ?>, '<?php echo $prod['estado'] === 'activo' ? 'inactivo' : 'activo'; ?>')">
                                            <?php echo $prod['estado'] === 'activo' ? '🚫 Desactivar' : '✅ Activar'; ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalProducto" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-top: 0;">Añadir Producto</h2>
            <form action="/greenland/index.php?action=producto_guardar" method="POST">
                <input type="hidden" id="id_producto" name="id_producto" value="">
                
                <div class="form-group">
                    <label for="nombre">Nombre del Producto/Servicio</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción (Opcional)</label>
                    <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio Base ($)</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0" required>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">💾 Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Funciones del Modal
        const modal = document.getElementById('modalProducto');
        
        function abrirModal() {
            document.getElementById('modalTitle').textContent = 'Añadir Producto';
            document.getElementById('id_producto').value = '';
            document.getElementById('nombre').value = '';
            document.getElementById('descripcion').value = '';
            document.getElementById('precio').value = '';
            modal.style.display = 'flex';
        }

        function cerrarModal() {
            modal.style.display = 'none';
        }

        function editarProducto(producto) {
            document.getElementById('modalTitle').textContent = 'Editar Producto';
            document.getElementById('id_producto').value = producto.id_producto;
            document.getElementById('nombre').value = producto.nombre;
            document.getElementById('descripcion').value = producto.descripcion;
            document.getElementById('precio').value = producto.precio;
            modal.style.display = 'flex';
        }

        // Función para cambiar estado vía Fetch API
        async function cambiarEstado(idProducto, nuevoEstado) {
            const accion = nuevoEstado === 'activo' ? 'activar' : 'desactivar';
            if (!confirm(`¿Estás seguro de que deseas ${accion} este producto?`)) return;

            try {
                const response = await fetch('/greenland/index.php?action=producto_cambiar_estado', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_producto: idProducto, estado: nuevoEstado })
                });
                
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            } catch (error) {
                console.error(error);
                alert('Error de conexión al intentar cambiar el estado.');
            }
        }
    </script>
</body>
</html>