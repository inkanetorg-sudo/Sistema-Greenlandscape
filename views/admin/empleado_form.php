<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $empleado ? 'Editar Jardinero' : 'Nuevo Jardinero'; ?></title>
    <style>
        body { font-family: system-ui, sans-serif; background-color: #f3f4f6; margin: 0; }
        .container { max-width: 500px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #1f2937; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; font-weight: bold; font-size: 0.9rem; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-size: 1rem; }
        .btn-guardar { width: 100%; background-color: #16a34a; color: white; padding: 0.75rem; border: none; border-radius: 6px; font-size: 1rem; font-weight: bold; cursor: pointer; margin-top: 1rem; }
        .btn-guardar:hover { background-color: #15803d; }
        .nav-volver { display: inline-block; margin-bottom: 1rem; color: #4b5563; text-decoration: none; font-size: 0.9rem; }
        .nav-volver:hover { text-decoration: underline; }
        .nota { font-size: 0.8rem; color: #6b7280; margin-top: 0.2rem; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar" style="padding: 1px;">
        <div class="container">
            <a href="/greenland/index.php?action=empleados" class="nav-volver">← Volver al Equipo</a>
            
            <h2><?php echo $empleado ? 'Editar Jardinero' : 'Registrar Jardinero'; ?></h2>

            <form action="/greenland/index.php?action=<?php echo $empleado ? 'empleado_actualizar' : 'empleado_guardar'; ?>" method="POST">
                
                <?php if ($empleado): ?>
                    <input type="hidden" name="id_usuario" value="<?php echo $empleado['id_usuario']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="nombre" required 
                           value="<?php echo $empleado ? htmlspecialchars($empleado['nombre']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Correo Electrónico (Para Login)</label>
                    <input type="email" name="email" required 
                           value="<?php echo $empleado ? htmlspecialchars($empleado['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" <?php echo $empleado ? '' : 'required'; ?>>
                    <?php if ($empleado): ?>
                        <div class="nota">Déjalo en blanco si no deseas cambiar la contraseña actual.</div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-guardar">
                    <?php echo $empleado ? 'Guardar Cambios' : 'Crear Jardinero'; ?>
                </button>
            </form>
        </div>
    </div>

</body>
</html>