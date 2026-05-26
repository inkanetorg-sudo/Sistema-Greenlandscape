<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Ruta de Trabajo</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .header { background-color: #16a34a; color: white; padding: 1rem; }
        .container { padding: 1rem; max-width: 600px; margin: 0 auto; }
        
        /* Contenedor del mapa en el celular */
        #mapa-empleado { width: 100%; height: 250px; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 1; }
        
        .date-filter { background: white; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; }
        .date-filter input { padding: 0.4rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
        
        .card { background: white; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 5px solid #fef08a; }
        .card.completado-card { border-left-color: #bbf7d0; }
        .card h3 { margin: 0 0 0.5rem 0; color: #1f2937; }
        .card p { margin: 0.2rem 0; color: #4b5563; font-size: 0.9rem; }
        
        .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; margin-bottom: 0.5rem; }
        .badge.pendiente { background-color: #fef08a; color: #854d0e; }
        .badge.completado { background-color: #bbf7d0; color: #166534; }
        
        .btn { display: block; width: 100%; padding: 0.75rem; background-color: #2563eb; color: white; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; margin-top: 1rem; text-align: center; }
        .btn:disabled { background-color: #9ca3af; cursor: not-allowed; }
    </style>
</head>
<body>

    <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0; font-size: 1.1rem;">Ruta: <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
        </div>
        <a href="/greenland/index.php?action=logout" style="color: white; text-decoration: none; font-size: 0.9rem; background: rgba(0,0,0,0.2); padding: 0.4rem 0.8rem; border-radius: 4px;">Salir</a>
    </div>

    <div class="container">
        
        <div class="date-filter">
            <label style="font-weight: bold; color: #374151;">Viendo fecha:</label>
            <input type="date" id="input-fecha-empleado" value="<?php echo $fecha; ?>">
        </div>

        <div id="mapa-empleado"></div>

        <?php if(empty($paradas)): ?>
            <div class="card" style="border-left-color: #ef4444;"><p style="text-align: center; font-weight: bold;">No tienes paradas programadas para esta fecha.</p></div>
        <?php else: ?>
            <?php foreach($paradas as $parada): ?>
                <div class="card <?php echo $parada['estado_visita'] === 'completado' ? 'completado-card' : ''; ?>" id="tarjeta-<?php echo $parada['id_detalle']; ?>">
                    <span class="badge <?php echo $parada['estado_visita']; ?>" id="badge-<?php echo $parada['id_detalle']; ?>">
                        <?php echo $parada['estado_visita']; ?>
                    </span>
                    
                    <h3><?php echo htmlspecialchars($parada['nombre_completo']); ?></h3>
                    <p>📍 <?php echo htmlspecialchars($parada['direccion']); ?></p>
                    <p>📞 Tel: <a href="tel:<?php echo htmlspecialchars($parada['telefono']); ?>" style="color: #2563eb; text-decoration: none; font-weight: bold;"><?php echo htmlspecialchars($parada['telefono']); ?></a></p>                  
                    <p>✂️ Tarea: <?php echo htmlspecialchars($parada['nombre_servicio']); ?></p>
                    
                    <?php if($parada['estado_visita'] === 'pendiente'): ?>
                        <button class="btn btn-completar" data-id="<?php echo $parada['id_detalle']; ?>">
                            Marcar como Completado
                        </button>
                    <?php else: ?>
                        <button class="btn" disabled>Servicio Finalizado</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/greenland/assets/js/empleado.js"></script>

    <script>
        // Cambiar de fecha recargando la URL correspondiente
        document.getElementById('input-fecha-empleado').addEventListener('change', function() {
            window.location.href = '/greenland/index.php?action=mi_ruta&fecha=' + this.value;
        });

        // Pasar las paradas de PHP a JavaScript de forma segura para pintar el mapa
        const paradasData = <?php echo json_encode($paradas); ?>;
        
        if (paradasData.length > 0) {
            // Inicializar el mapa centrado en la primera parada
            const mapEmpleado = L.map('mapa-empleado').setView([paradasData[0].latitud, paradasData[0].longitud], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapEmpleado);

            const coordenadasLinea = [];

            paradasData.forEach((parada, i) => {
                const lat = parseFloat(parada.latitud);
                const lng = parseFloat(parada.longitud);
                coordenadasLinea.push([lat, lng]);

                // Crear un marcador de color personalizado basado en el estado
                // Usamos círculos de Leaflet estilizados como marcadores limpios
                const colorMarcador = parada.estado_visita === 'completado' ? '#16a34a' : '#eab308';
                
                L.circleMarker([lat, lng], {
                    radius: 10,
                    fillColor: colorMarcador,
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 0.9
                })
                .addTo(mapEmpleado)
                .bindPopup(`<b>${i+1}. ${parada.nombre_completo}</b><br>Tarea: ${parada.nombre_servicio}<br>Estado: ${parada.estado_visita}`);
            });

            // Dibujar la línea de la ruta que conecta las casas ordenadamente
            L.polyline(coordenadasLinea, { color: '#2563eb', weight: 3, dashArray: '5, 5' }).addTo(mapEmpleado);
            
            // Ajustar el zoom automáticamente para que abarque todas las paradas perfectamente
            const group = new L.featureGroup(coordenadasLinea.map(c => L.marker(c)));
            mapEmpleado.fitBounds(group.getBounds().pad(0.1));
        } else {
            // Mapa por defecto si no hay ruta ese día
            const mapEmpleado = L.map('mapa-empleado').setView([-12.075, -77.090], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapEmpleado);
        }
    </script>
</body>
</html>