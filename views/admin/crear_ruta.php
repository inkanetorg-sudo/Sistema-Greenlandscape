<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Creador Visual de Rutas</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        .creator-wrapper { display: flex; height: calc(100vh - 60px); width: 100%; margin-top: 60px; }
        #sidebar { width: 350px; background: white; padding: 1.5rem; box-shadow: 2px 0 5px rgba(0,0,0,0.1); display: flex; flex-direction: column; box-sizing: border-box; z-index: 1000; overflow-y: auto; }
        #mapa { flex-grow: 1; height: 100%; z-index: 1; }
        
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; font-weight: bold; margin-bottom: 0.4rem; color: #374151; }
        select, input { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; box-sizing: border-box; }
        .btn-guardar { width: 100%; padding: 0.75rem; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; margin-top: auto; }
        .btn-guardar:hover { background-color: #15803d; }
        
        ol { padding-left: 1.2rem; color: #4b5563; }
        li { margin-bottom: 0.5rem; }
        
        #lista-paradas li { 
            margin-bottom: 0.5rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background: #f9fafb; 
            padding: 0.5rem; 
            border-radius: 4px;
            border: 1px solid #e5e7eb;
            font-size: 0.95rem;
        }
        .btn-eliminar { background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; padding: 0.2rem 0.5rem; font-size: 0.8rem; }
        .btn-eliminar:hover { background: #dc2626; }

        @media (min-width: 768px) { .creator-wrapper { height: 100vh; margin-top: 0; } }
        @media (max-width: 767px) {
            .creator-wrapper { flex-direction: column; height: auto; min-height: calc(100vh - 60px); }
            #sidebar { width: 100%; height: auto; }
            #mapa { min-height: 400px; }
        }
    </style>
</head>
<body>
    
    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar">
        <div class="creator-wrapper">
            <div id="sidebar">
                <div>
                    <h2>Crear Ruta Diaria</h2>
                    <hr style="border: 0; border-top: 1px solid #e5e7eb; margin-bottom: 1rem;">
                    
                    <?php if (isset($rezagados) && !empty($rezagados)): ?>
                    <div style="background-color: #fef2f2; border-left: 5px solid #ef4444; padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px;">
                        <h3 style="margin-top: 0; color: #b91c1c; font-size: 1.1rem;">⚠️ Pendientes Anteriores</h3>
                        <p style="font-size: 0.85rem; color: #7f1d1d; margin-bottom: 1rem;">
                            Reprograma estas tareas al jardinero seleccionado abajo:
                        </p>
                        
                        <div style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($rezagados as $rezago): ?>
                                <div id="rezagado-<?php echo $rezago['id_detalle']; ?>" style="background: white; border: 1px solid #fca5a5; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 4px; font-size: 0.85rem; transition: opacity 0.3s;">
                                    <b>👤 <?php echo htmlspecialchars($rezago['nombre_completo']); ?></b><br>
                                    🛠️ <?php echo htmlspecialchars($rezago['nombre_servicio']); ?><br>
                                    <span style="color: #6b7280; font-size: 0.75rem;">
                                        📅 Era para: <?php echo htmlspecialchars($rezago['fecha_ruta']); ?> (<?php echo htmlspecialchars($rezago['jardinero_anterior']); ?>)
                                    </span>
                                    
                                    <button onclick="reprogramarRezagado(<?php echo $rezago['id_detalle']; ?>, <?php echo $rezago['id_cliente']; ?>, <?php echo $rezago['id_servicio']; ?>, '<?php echo addslashes($rezago['nombre_completo']); ?>', '<?php echo addslashes($rezago['nombre_servicio']); ?>', this)" 
                                            style="display: block; width: 100%; margin-top: 0.5rem; background: #ef4444; color: white; border: none; padding: 0.4rem; border-radius: 3px; cursor: pointer;">
                                        ➕ Añadir a la ruta de hoy
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="empleado">1. Selecciona Jardinero:</label>
                        <select id="empleado">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach($empleados as $emp): ?>
                                <option value="<?php echo $emp['id_usuario']; ?>"><?php echo htmlspecialchars($emp['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fecha">2. Fecha de la Ruta:</label>
                        <input type="date" id="fecha" value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="servicio">3. Servicio a realizar:</label>
                        <select id="servicio">
                            <?php foreach($servicios as $srv): ?>
                                <option value="<?php echo $srv['id_servicio']; ?>"><?php echo htmlspecialchars($srv['nombre_servicio']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>4. Orden de Visitas:</label>
                        <p style="font-size: 0.85rem; color: #6b7280; margin-top: 0;">Haz clic en el mapa o reprograma rezagados.</p>
                        <ol id="lista-paradas"></ol>
                    </div>
                </div>

                <button class="btn-guardar" id="btnGuardar" style="margin-top: 2rem;">Guardar y Asignar Ruta</button>
            </div>

            <div id="mapa"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let rutaSeleccionada = [];

        // 1. Inicializar el mapa
        const map = L.map('mapa').setView([-12.075, -77.090], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

        const clientes = <?php echo json_encode($clientes); ?>;

        // 2. Función reutilizable para agregar una parada (del mapa o rezagado)
        function agregarParada(idCliente, idServicio, nombreCliente, nombreServicio, idDetalleAntiguo = null) {
            const existe = rutaSeleccionada.some(parada => parada.id_cliente === idCliente);

            if (!existe) {
                // Guardamos el ID antiguo en el payload (null si viene del mapa normal)
                rutaSeleccionada.push({ 
                    id_cliente: idCliente, 
                    id_servicio: parseInt(idServicio),
                    id_detalle_antiguo: idDetalleAntiguo 
                });
                
                const lista = document.getElementById('lista-paradas');
                const li = document.createElement('li');
                
                const spanTexto = document.createElement('span');
                spanTexto.innerHTML = `<b>${nombreCliente}</b><br><small style="color:#6b7280;">🛠️ ${nombreServicio}</small>`;
                
                const btnEliminar = document.createElement('button');
                btnEliminar.textContent = '✖';
                btnEliminar.className = 'btn-eliminar';
                
                // Si eliminamos la parada de la lista
                btnEliminar.addEventListener('click', function() {
                    rutaSeleccionada = rutaSeleccionada.filter(parada => parada.id_cliente !== idCliente);
                    lista.removeChild(li);
                    
                    // Si era un rezagado, hacemos que su tarjeta roja vuelva a aparecer
                    if (idDetalleAntiguo) {
                        const tarjeta = document.getElementById('rezagado-' + idDetalleAntiguo);
                        if (tarjeta) {
                            tarjeta.style.display = 'block';
                            setTimeout(() => tarjeta.style.opacity = "1", 10);
                        }
                    }
                });

                li.appendChild(spanTexto);
                li.appendChild(btnEliminar);
                lista.appendChild(li);
            }
        }

        // 3. Marcadores del mapa
        clientes.forEach(cliente => {
            const marcador = L.marker([cliente.latitud, cliente.longitud]).addTo(map);
            marcador.bindPopup(`<b>${cliente.nombre_completo}</b>`);

            marcador.on('click', function() {
                const selectServicio = document.getElementById('servicio');
                const idServicio = selectServicio.value;
                const nombreServicio = selectServicio.options[selectServicio.selectedIndex].text;

                if (!idServicio) { alert("Por favor, selecciona un servicio."); return; }
                agregarParada(cliente.id_cliente, idServicio, cliente.nombre_completo, nombreServicio, null);
            });
        });

        // 4. Función de Rezagados (Solo visual, ya NO hace Fetch a la BD)
        function reprogramarRezagado(idDetalleAntiguo, idCliente, idServicio, nombreCliente, nombreServicio, botonElemento) {
            
            if(!document.getElementById('empleado').value) {
                alert("Primero selecciona el Jardinero que atenderá este rezago hoy.");
                return;
            }

            // Agregamos a la lista visual pasándole el ID antiguo
            agregarParada(idCliente, idServicio, nombreCliente, nombreServicio, idDetalleAntiguo);

            // Ocultamos la tarjeta roja visualmente
            const tarjeta = botonElemento.parentElement;
            tarjeta.style.opacity = "0";
            setTimeout(() => tarjeta.style.display = 'none', 300);
        }

        // 5. Enviar al backend (AQUÍ SÍ SE GUARDA TODO)
        document.getElementById('btnGuardar').addEventListener('click', async function() {
            const idEmpleado = document.getElementById('empleado').value;
            const fechaRuta = document.getElementById('fecha').value;

            if (!idEmpleado || rutaSeleccionada.length === 0) {
                alert('Selecciona un empleado y al menos un cliente en el mapa (o rezagados).');
                return;
            }

            const payload = { id_empleado: parseInt(idEmpleado), fecha: fechaRuta, paradas: rutaSeleccionada };

            try {
                const respuesta = await fetch('/greenland/index.php?action=guardar_ruta', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const resultado = await respuesta.json();
                if (resultado.success) {
                    alert('¡Ruta creada y asignada con éxito!');
                    window.location.reload();
                } else {
                    alert('Error al guardar: ' + resultado.error);
                }
            } catch (error) {
                console.error(error);
                alert('Error de conexión con el servidor.');
            }
        });
    </script>
</body>
</html>