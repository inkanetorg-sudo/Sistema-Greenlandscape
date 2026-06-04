<!DOCTYPE html>
<html lang="es">
<head>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background-color: #f3f4f6; }
        
        /* Estilos generales compartidos */
        .content-with-sidebar { padding: 2rem; }
        .card, .marketing-card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        
        .btn-marketing, .btn-primary { background-color: #3b82f6; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: background 0.3s; text-decoration: none; display: inline-block;}
        .btn-marketing:hover, .btn-primary:hover { background-color: #2563eb; }
        
        /* Estilos de Tabla */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #f9fafb; padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #374151; }
        td { padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
        tr:hover { background-color: #f9fafb; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>
    <div class="content-with-sidebar" style="padding: 2rem;">
        <h1>Módulo de Marketing</h1>

        <div class="marketing-card">
            <h2>📢 Aviso de Inicio de Temporada</h2>
            <p>Envía un email general a toda tu base de clientes anunciando el inicio de los servicios de temporada.</p>
            <form action="/greenland/index.php?action=marketing_aviso_temporada" method="POST" enctype="multipart/form-data">
				<input type="text" name="asunto" placeholder="Ej: ¡Llegó la Primavera! Prepara tu jardín" style="width:100%; padding: 0.5rem; margin-bottom:1rem;" required>
				
				<textarea name="mensaje" placeholder="Publicidad o puntos importantes..." style="width:100%; height:100px; margin-bottom:1rem;" required></textarea>
				
				<div style="margin-bottom: 1rem; padding: 1rem; background: #f8f9fa; border: 1px dashed #cbd5e1; border-radius: 4px;">
					<label style="display: block; font-weight: bold; margin-bottom: 0.5rem; color: #475569;">📎 Adjuntar Archivo (PDF o Imagen)</label>
					<input type="file" name="archivo_adjunto" accept=".pdf, .jpg, .jpeg, .png">
				</div>

				<button type="submit" class="btn-marketing">🚀 Enviar a todos los clientes</button>
			</form>
        </div>

        <div class="marketing-card">
            <h2>🎯 Segmentación: Venta de Servicios</h2>
            <p>Selecciona un servicio y envía una oferta solo a clientes que <b>NO</b> lo tienen contratado.</p>
            <form action="/greenland/index.php?action=marketing_segmentado" method="POST">
                <!-- Dentro de tu formulario de segmentación -->
				<select name="id_servicio" style="padding:0.5rem; width:200px;">
					<?php
					$servicios = $this->db->query("SELECT id_servicio, nombre_servicio FROM servicios")->fetchAll();
					foreach($servicios as $s) {
						echo "<option value='{$s['id_servicio']}'>{$s['nombre_servicio']}</option>";
					}
					?>
				</select>
                <button type="submit" class="btn-marketing" style="background: #2563eb;">🔍 Filtrar y Enviar</button>
            </form>
        </div>
    </div>
</body>
</html>