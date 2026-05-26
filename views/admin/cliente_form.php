<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $cliente ? 'Editar Cliente' : 'Nuevo Cliente'; ?></title>
    <style>
        body { font-family: system-ui, sans-serif; background-color: #f3f4f6; margin: 0; padding: 2rem; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #1f2937; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; font-weight: bold; font-size: 0.9rem; }
        input[type="text"], input[type="number"] { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 1rem; }
        .coordenadas { display: flex; gap: 1rem; }
        .coordenadas .form-group { flex: 1; }
        .btn-guardar { width: 100%; background-color: #16a34a; color: white; padding: 0.75rem; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; margin-top: 1rem; }
        .btn-guardar:hover { background-color: #15803d; }
        .nav-volver { display: inline-block; margin-bottom: 1rem; color: #4b5563; text-decoration: none; font-size: 0.9rem; }
        .nav-volver:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="container">
        <a href="/greenland/index.php?action=clientes" class="nav-volver">← Volver al Listado</a>
        
        <h2><?php echo $cliente ? 'Editar Datos del Cliente' : 'Registrar Nuevo Cliente'; ?></h2>

        <form action="/greenland/index.php?action=<?php echo $cliente ? 'cliente_actualizar' : 'cliente_guardar'; ?>" method="POST">
            
            <?php if ($cliente): ?>
                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre_completo" required 
                       value="<?php echo $cliente ? htmlspecialchars($cliente['nombre_completo']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion" placeholder="Ej: Av. La Marina 1500, San Miguel" required 
                       value="<?php echo $cliente ? htmlspecialchars($cliente['direccion']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" 
                       value="<?php echo $cliente ? htmlspecialchars($cliente['telefono']) : ''; ?>">
            </div>

            <div class="coordenadas">
                <div class="form-group">
                    <label>Latitud (Mapa)</label>
                    <input type="text" name="latitud" placeholder="-12.079444" required 
                           value="<?php echo $cliente ? htmlspecialchars($cliente['latitud']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Longitud (Mapa)</label>
                    <input type="text" name="longitud" placeholder="-77.093611" required 
                           value="<?php echo $cliente ? htmlspecialchars($cliente['longitud']) : ''; ?>">
                </div>
            </div>
            <p style="font-size: 0.8rem; color: #6b7280; margin-top: -10px;">*Las coordenadas son obligatorias para que el cliente aparezca en el creador de rutas.</p>

            <button type="submit" class="btn-guardar">
                <?php echo $cliente ? 'Guardar Cambios' : 'Crear Cliente'; ?>
            </button>
        </form>
    </div>

</body>
</html>