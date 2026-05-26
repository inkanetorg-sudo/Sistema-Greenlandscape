<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
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

        /* --- ESTILOS RESPONSIVOS PARA LA TABLA MÓVIL --- */
        @media (max-width: 768px) {
            /* Ocultar las cabeceras de la tabla */
            table thead { display: none; }
            
            /* Convertir tabla y filas en bloques */
            table, table tbody, table tr, table td { display: block; width: 100%; box-sizing: border-box; }
            
            table tr {
                margin-bottom: 1.5rem;
                border: 1px solid #d1d5db;
                border-radius: 8px;
                padding: 1rem;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            
            table td {
                text-align: right; /* Empuja el valor a la derecha */
                padding: 0.5rem 0;
                border-bottom: 1px solid #f3f4f6;
                position: relative;
            }
            
            /* Quitar el borde inferior al último elemento (los botones) */
            table td:last-child {
                border-bottom: none;
                padding-top: 1rem;
                display: flex;
                justify-content: flex-end; /* Alinear botones a la derecha */
                gap: 0.5rem;
            }
            
            /* Mostrar el nombre de la columna usando el atributo data-label */
            table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                font-weight: 600;
                color: #4b5563;
                text-align: left;
            }

            /* Ajustar el header para que el botón de Nuevo Cliente no se aplaste */
            .header-acciones {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    
    <?php include __DIR__ . '/../layout/admin_sidebar.php'; ?>

    <div class="content-with-sidebar" style="padding: 1px;">
        <div class="container">
            
            <div class="header-acciones">
                <h2>Directorio de Clientes</h2>
                <a href="/greenland/index.php?action=cliente_crear" class="btn-nuevo">+ Nuevo Cliente</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($clientes)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280;">No hay clientes registrados en el sistema.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($clientes as $cliente): ?>
                            <tr>
                                <td data-label="ID:"><?php echo $cliente['id_cliente']; ?></td>
                                <td data-label="Nombre:"><?php echo htmlspecialchars($cliente['nombre_completo']); ?></td>
                                <td data-label="Dirección:"><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                                <td data-label="Teléfono:"><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                <td data-label="Acciones:" class="acciones">
                                    <a href="/greenland/index.php?action=cliente_editar&id=<?php echo $cliente['id_cliente']; ?>" class="btn-editar">Editar</a>
                                    <a href="/greenland/index.php?action=cliente_eliminar&id=<?php echo $cliente['id_cliente']; ?>" class="btn-eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?');">Eliminar</a>
									<a href="index.php?action=facturar_cliente&id=<?php echo $cliente['id_cliente']; ?>" style="background: #10b981; color: white; padding: 0.4rem 0.8rem; border-radius: 4px; text-decoration: none; font-size: 0.8rem;">📄 Facturar</a>
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