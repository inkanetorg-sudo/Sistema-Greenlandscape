<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Empleados</title>
    <style>
        body { font-family: system-ui, sans-serif; background-color: #f3f4f6; margin: 0; }
        .container { max-width: 1000px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header-acciones { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        h2 { margin: 0; color: #1f2937; }
        .btn-nuevo { background-color: #16a34a; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .btn-nuevo:hover { background-color: #15803d; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #f9fafb; color: #4b5563; font-weight: 600; }
        
        .acciones a { text-decoration: none; padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.85rem; margin-right: 0.3rem; color: white; }
        .btn-editar { background-color: #3b82f6; }
        .btn-editar:hover { background-color: #2563eb; }
        .btn-eliminar { background-color: #ef4444; }
        .btn-eliminar:hover { background-color: #dc2626; }
    </style>
</head>
<body>

    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar" style="padding: 1px;">
        <div class="container">
            <div class="header-acciones">
                <h2>Equipo de Trabajo (Jardineros)</h2>
                <a href="/greenland/index.php?action=empleado_crear" class="btn-nuevo">+ Nuevo Jardinero</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo de Acceso</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($empleados)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280;">No hay jardineros registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($empleados as $emp): ?>
                            <tr>
                                <td><?php echo $emp['id_usuario']; ?></td>
                                <td><?php echo htmlspecialchars($emp['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                <td><span style="background: #e5e7eb; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; text-transform: uppercase;"><?php echo htmlspecialchars($emp['rol']); ?></span></td>
                                <td class="acciones">
                                    <a href="/greenland/index.php?action=empleado_editar&id=<?php echo $emp['id_usuario']; ?>" class="btn-editar">Editar</a>
                                    <a href="/greenland/index.php?action=empleado_eliminar&id=<?php echo $emp['id_usuario']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar a este jardinero? Esto borrará también su historial de rutas.');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>