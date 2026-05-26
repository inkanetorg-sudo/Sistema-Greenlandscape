<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $servicio ? 'Editar Servicio' : 'Nuevo Servicio'; ?></title>
    <style>
        body { font-family: system-ui, sans-serif; background-color: #f3f4f6; margin: 0; }
        .container { max-width: 600px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #1f2937; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; font-weight: bold; font-size: 0.9rem; }
        input[type="text"], textarea { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 1rem; font-family: inherit; }
        .btn-guardar { width: 100%; background-color: #16a34a; color: white; padding: 0.75rem; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; margin-top: 1rem; }
        .btn-guardar:hover { background-color: #15803d; }
        .nav-volver { display: inline-block; margin-bottom: 1rem; color: #4b5563; text-decoration: none; font-size: 0.9rem; }
        .nav-volver:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar" style="padding: 1px;">
        <div class="container">
            <a href="/greenland/index.php?action=servicios" class="nav-volver">← Volver al Catálogo</a>
            
            <h2><?php echo $servicio ? 'Editar Servicio' : 'Registrar Nuevo Servicio'; ?></h2>

            <form action="/greenland/index.php?action=<?php echo $servicio ? 'servicio_actualizar' : 'servicio_guardar'; ?>" method="POST">
                
                <?php if ($servicio): ?>
                    <input type="hidden" name="id_servicio" value="<?php echo $servicio['id_servicio']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Nombre del Servicio</label>
                    <input type="text" name="nombre_servicio" placeholder="Ej: Poda Básica, Fumigación, etc." required 
                           value="<?php echo $servicio ? htmlspecialchars($servicio['nombre_servicio']) : ''; ?>">
                </div>
				
				<div class="form-group">
					<label>Duración Estimada (minutos)</label>
					<input type="number" name="duracion_estimada" required 
						   value="<?php echo $servicio ? htmlspecialchars($servicio['duracion_estimada']) : ''; ?>">
				</div>
				
				<div class="form-group">
                    <label>Precio del Servicio ($)</label>
                    <input type="number" step="0.01" name="precio" required 
                           value="<?php echo $servicio ? htmlspecialchars($servicio['precio']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Descripción detallada</label>
                    <textarea name="descripcion" rows="4" placeholder="¿En qué consiste este servicio?"><?php echo $servicio ? htmlspecialchars($servicio['descripcion']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn-guardar">
                    <?php echo $servicio ? 'Guardar Cambios' : 'Crear Servicio'; ?>
                </button>
            </form>
        </div>
    </div>

</body>
</html>