<style>
    /* Estilos globales para el sidebar */
    .admin-sidebar {
        width: 250px;
        background-color: #1f2937;
        color: white;
        display: flex;
        flex-direction: column;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 9999;
    }
    .admin-sidebar-header {
        padding: 1.5rem;
        background-color: #111827;
        text-align: center;
        border-bottom: 1px solid #374151;
    }
    .admin-sidebar-header h3 {
        margin: 0;
        color: #10b981;
        letter-spacing: 1px;
    }
    .admin-sidebar-nav {
        flex-grow: 1;
        padding: 1rem 0;
    }
    .admin-sidebar-nav a {
        display: block;
        padding: 1rem 1.5rem;
        color: #d1d5db;
        text-decoration: none;
        transition: background-color 0.2s;
        border-left: 4px solid transparent;
        font-size: 0.95rem;
    }
    .admin-sidebar-nav a:hover {
        background-color: #374151;
        color: white;
        border-left-color: #10b981;
    }
    .admin-sidebar-footer {
        padding: 1.5rem;
        border-top: 1px solid #374151;
        font-size: 0.9rem;
    }
    .admin-sidebar-footer p {
        margin: 0 0 1rem 0;
        color: #9ca3af;
    }
    .btn-logout {
        display: block;
        text-align: center;
        background-color: #ef4444;
        color: white;
        text-decoration: none;
        padding: 0.6rem;
        border-radius: 4px;
        font-weight: bold;
    }
    .btn-logout:hover {
        background-color: #dc2626;
    }
    
    /* Clase para empujar el contenido hacia la derecha */
    .content-with-sidebar {
        margin-left: 250px;
        width: calc(100% - 250px);
        min-height: 100vh;
        box-sizing: border-box;
    }
</style>

<div class="admin-sidebar">
    <div class="admin-sidebar-header">
        <h3>SISTEMA RUTAS</h3>
        <small style="color: #9ca3af;">Panel de Administración</small>
    </div>
    
    <div class="admin-sidebar-nav">
        <a href="/greenland/index.php?action=dashboard">📍 Seguimiento en Vivo</a>
        <a href="/greenland/index.php?action=crear_ruta">🗺️ Creador de Rutas</a>
        <a href="/greenland/index.php?action=clientes">👥 Directorio de Clientes</a>
        <a href="/greenland/index.php?action=empleados">👷 Equipo / Jardineros</a>
        <a href="/greenland/index.php?action=servicios">🛠️ Catálogo de Servicios</a>
    </div>

    <div class="admin-sidebar-footer">
        <p>Hola, <b><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Admin'); ?></b></p>
        <a href="/greenland/index.php?action=logout" class="btn-logout">Cerrar Sesión</a>
    </div>
</div>