<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento en Vivo</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        
        .map-wrapper { display: flex; height: 100vh; width: 100%; }
        
        #sub-panel { width: 300px; background: white; padding: 1.5rem; box-shadow: 2px 0 5px rgba(0,0,0,0.1); z-index: 1000; box-sizing: border-box; display: flex; flex-direction: column; overflow-y: auto;}
        #sub-panel h2 { margin-top: 0; color: #1f2937; }
        
        #mapa { flex-grow: 1; height: 100%; z-index: 1; }
        
        .leyenda { margin-top: 2rem; }
        .item-leyenda { display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.95rem; color: #374151; }
        .color-box { width: 15px; height: 15px; border-radius: 50%; margin-right: 10px; }
        .bg-red { background-color: #cb2b3e; }
        .bg-green { background-color: #2aad27; }

        .date-filter { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; }
        .date-filter label { display: block; font-weight: bold; color: #374151; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .date-filter input, .date-filter select { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; box-sizing: border-box; margin-bottom: 1rem;}

        .btn-ver-lista { width: 100%; padding: 0.75rem; background-color: #3b82f6; color: white; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 0.5rem;}
        .btn-ver-lista:hover { background-color: #2563eb; }

        /* --- ESTILOS DEL MODAL POPUP --- */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 2rem; border-radius: 8px; width: 90%; max-width: 800px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; }
        .modal-header h2 { margin: 0; color: #1f2937; }
        .close-btn { color: #9ca3af; font-size: 28px; font-weight: bold; cursor: pointer; background: none; border: none; padding: 0; }
        .close-btn:hover { color: #1f2937; }
        
        /* --- ESTILOS DE LA TABLA DEL MODAL --- */
        .tabla-rutas { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.95rem; }
        .tabla-rutas th, .tabla-rutas td { border-bottom: 1px solid #e5e7eb; padding: 0.75rem; text-align: left; }
        .tabla-rutas th { background-color: #f9fafb; color: #4b5563; font-weight: 600; }
        .grupo-jardinero td { background-color: #f3f4f6; font-weight: bold; color: #1f2937; padding-top: 1rem; border-bottom: 2px solid #d1d5db; }
        .badge-tabla { padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
        .badge-pendiente { background-color: #fef08a; color: #854d0e; }
        .badge-completado { background-color: #bbf7d0; color: #166534; }
    </style>
</head>
<body>
    
    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar">
        
        <div class="map-wrapper">
            
            <div id="sub-panel">
                <div>
                    <h2>Panel en Vivo</h2>
                    <p style="color: #4b5563; font-size: 0.9rem; margin-bottom: 0.5rem;">Monitor de rutas del sistema.</p>
                </div>

                <div class="date-filter">
                    <label for="fecha-dashboard">📅 Filtrar por fecha:</label>
                    <input type="date" id="fecha-dashboard" value="<?php echo date('Y-m-d'); ?>">

                    <label for="filtro-empleado">👷 Filtrar por Jardinero:</label>
                    <select id="filtro-empleado">
                        <option value="todos">Todos los jardineros</option>
                        <?php if(isset($empleados) && !empty($empleados)): ?>
                            <?php foreach($empleados as $emp): ?>
                                <option value="<?php echo $emp['id_usuario']; ?>"><?php echo htmlspecialchars($emp['nombre']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <button class="btn-ver-lista" id="btnAbrirModal">📋 Ver Listado de Rutas</button>

                <div class="leyenda">
                    <h3 style="color: #1f2937; font-size: 1.1rem;">Leyenda</h3>
                    <div class="item-leyenda"><div class="color-box bg-red"></div> Pendiente</div>
                    <div class="item-leyenda"><div class="color-box bg-green"></div> Completado</div>
                </div>
                
                <p style="font-size: 0.8rem; color: #6b7280; background: #f3f4f6; padding: 0.5rem; border-radius: 4px; border: 1px solid #e5e7eb; margin-top: auto;">
                    ⏳ Actualización auto: 10s.
                </p>
            </div>

            <div id="mapa"></div>

        </div>
    </div>

    <div id="modalRutas" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Listado de Asignaciones</h2>
                <button class="close-btn" id="btnCerrarModal">&times;</button>
            </div>
            <div id="contenidoListaRutas">
                </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('mapa').setView([-12.075, -77.090], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        const capaMarcadores = L.layerGroup().addTo(map);

        const iconoPendiente = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });
        const iconoCompletado = new L.Icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        let marcadores = {};

        function construirTablaModal(rutas) {
            const contenedor = document.getElementById('contenidoListaRutas');
            
            if (rutas.length === 0) {
                contenedor.innerHTML = '<p style="text-align:center; color:#6b7280; padding: 2rem;">No hay rutas asignadas para esta selección.</p>';
                return;
            }

            const agrupado = {};
            rutas.forEach(r => {
                if (!agrupado[r.nombre_jardinero]) { agrupado[r.nombre_jardinero] = []; }
                agrupado[r.nombre_jardinero].push(r);
            });

            let html = '<table class="tabla-rutas">';
            html += '<thead><tr><th>#</th><th>Cliente</th><th>Servicio</th><th>Precio</th><th>Estado</th></tr></thead>';
            html += '<tbody>';

            for (const jardinero in agrupado) {
                let totalJardinero = 0;
                
                // Fila del Jardinero
                html += `<tr class="grupo-jardinero"><td colspan="5">👷 ${jardinero}</td></tr>`;
                
                // Filas de las paradas
                agrupado[jardinero].forEach(parada => {
                    totalJardinero += parseFloat(parada.precio);
                    const claseBadge = parada.estado_visita === 'completado' ? 'badge-completado' : 'badge-pendiente';
                    
                    html += `
                        <tr>
                            <td style="color:#6b7280;">${parada.orden_visita}</td>
                            <td><b>${parada.nombre_cliente}</b></td>
                            <td>${parada.nombre_servicio}</td>
                            <td>$${parseFloat(parada.precio).toFixed(2)}</td>
                            <td>
                                <select data-id="${parada.id_detalle}" onchange="cambiarEstadoAdmin(this)" style="padding: 0.3rem; border-radius: 4px; border: 1px solid #d1d5db; font-weight: bold; background-color: ${parada.estado_visita === 'completado' ? '#dcfce7' : '#fef08a'}; color: ${parada.estado_visita === 'completado' ? '#166534' : '#854d0e'}; outline: none; cursor: pointer;">
                                    <option value="pendiente" ${parada.estado_visita === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                    <option value="completado" ${parada.estado_visita === 'completado' ? 'selected' : ''}>Completado</option>
                                </select>
                            </td>
                        </tr>
                    `;
                });

                // Fila de Total para este jardinero
                html += `
                    <tr style="background-color: #e5e7eb;">
                        <td colspan="3" style="text-align: right;"><b>TOTAL ${jardinero.toUpperCase()}:</b></td>
                        <td colspan="2"><b>$${totalJardinero.toFixed(2)}</b></td>
                    </tr>
                `;
            }

            html += '</tbody></table>';
            contenedor.innerHTML = html;
        }

        // Nueva función para enviar el cambio de estado desde el modal
        async function cambiarEstadoAdmin(selectElement) {
            const idDetalle = selectElement.getAttribute('data-id');
            const nuevoEstado = selectElement.value;
            
            // Deshabilitamos temporalmente para evitar doble clic
            selectElement.disabled = true;

            try {
                const respuesta = await fetch('/greenland/index.php?action=admin_cambiar_estado', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_detalle: idDetalle, estado: nuevoEstado })
                });
                
                const data = await respuesta.json();
                
                if (data.success) {
                    // Cambiamos el color de fondo del select según el nuevo estado
                    selectElement.style.backgroundColor = nuevoEstado === 'completado' ? '#dcfce7' : '#fef08a';
                    selectElement.style.color = nuevoEstado === 'completado' ? '#166534' : '#854d0e';
                    // Forzamos actualización del mapa en vivo sin borrar capas para que cambie el icono
                    actualizarMapa(false);
                } else {
                    alert('Error al guardar el estado.');
                }
            } catch (error) {
                console.error("Error en la red:", error);
                alert("Error de conexión.");
            }
            
            selectElement.disabled = false;
        }

        async function actualizarMapa(limpiar = false) {
            try {
                // CAPTURAMOS AMBOS FILTROS
                const fechaSeleccionada = document.getElementById('fecha-dashboard').value;
                const empleadoSeleccionado = document.getElementById('filtro-empleado').value;
                
                if (limpiar) {
                    capaMarcadores.clearLayers();
                    marcadores = {};
                }

                // ENVIAMOS AMBOS FILTROS EN LA URL
                const respuesta = await fetch(`/greenland/index.php?action=api_rutas_hoy&fecha=${fechaSeleccionada}&empleado=${empleadoSeleccionado}`);
                const rutas = await respuesta.json();

                construirTablaModal(rutas);

                rutas.forEach(parada => {
                    const id = parada.id_detalle;
                    const esCompletado = parada.estado_visita === 'completado';
                    const icono = esCompletado ? iconoCompletado : iconoPendiente;

                    if (marcadores[id]) {
                        marcadores[id].setIcon(icono);
                    } else {
                        const marcador = L.marker([parada.latitud, parada.longitud], {icon: icono});
                        marcador.bindPopup(`
                            <b>${parada.nombre_cliente}</b><br>
                            🛠️ <b>Servicio:</b> ${parada.nombre_servicio}<br>
                            👷 <b>Jardinero:</b> ${parada.nombre_jardinero}<br>
                            📌 <b>Estado:</b> ${parada.estado_visita.toUpperCase()}
                        `);
                        capaMarcadores.addLayer(marcador);
                        marcadores[id] = marcador;
                    }
                });
            } catch (error) {
                console.error("Error actualizando datos:", error);
            }
        }

        const modal = document.getElementById("modalRutas");
        const btnAbrir = document.getElementById("btnAbrirModal");
        const btnCerrar = document.getElementById("btnCerrarModal");

        btnAbrir.onclick = function() { modal.style.display = "block"; }
        btnCerrar.onclick = function() { modal.style.display = "none"; }
        window.onclick = function(event) { if (event.target == modal) { modal.style.display = "none"; } }

        // ACTUALIZAR AL CAMBIAR FECHA O JARDINERO
        document.getElementById('fecha-dashboard').addEventListener('change', () => actualizarMapa(true));
        document.getElementById('filtro-empleado').addEventListener('change', () => actualizarMapa(true));

        actualizarMapa();
        setInterval(() => actualizarMapa(false), 10000);
    </script>
</body>
</html>