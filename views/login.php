<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Rutas</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 90%; max-width: 400px; box-sizing: border-box; }
        h2 { text-align: center; color: #16a34a; margin-top: 0; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 600; font-size: 0.9rem; }
        
        /* Ajustamos todos los inputs para que tengan el mismo estilo */
        input[type="email"], input[type="password"], input[type="text"] { 
            width: 100%; 
            padding: 0.75rem; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            box-sizing: border-box; 
            font-size: 1rem; 
        }
        input:focus { outline: none; border-color: #16a34a; box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2); }
        
        /* Estilos específicos para el contenedor de la contraseña y el ojito */
        .password-container { position: relative; }
        .password-container input { padding-right: 2.5rem; /* Hacemos espacio para el ícono */ }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: #6b7280;
            padding: 0;
        }
        .toggle-password:focus { outline: none; }

        .btn { width: 100%; padding: 0.75rem; background-color: #16a34a; color: white; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; font-weight: bold; transition: background-color 0.2s; margin-top: 10px;}
        .btn:hover { background-color: #15803d; }
        .error { background-color: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 6px; margin-bottom: 1.2rem; font-size: 0.85rem; text-align: center; border: 1px solid #f87171; }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Panel de Acceso</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="/greenland/index.php?action=procesar_login" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <button type="button" id="btnToggle" class="toggle-password" title="Mostrar/Ocultar">👁️</button>
                </div>
            </div>
            
            <button type="submit" class="btn">Ingresar al Sistema</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnToggle = document.getElementById('btnToggle');
            const inputPassword = document.getElementById('password');

            btnToggle.addEventListener('click', function() {
                // Verificamos si el campo está como password o como texto
                if (inputPassword.type === 'password') {
                    inputPassword.type = 'text'; // Mostramos la clave
                    btnToggle.textContent = '🙈'; // Cambiamos el ícono
                } else {
                    inputPassword.type = 'password'; // Ocultamos la clave
                    btnToggle.textContent = '👁️'; // Restauramos el ícono
                }
            });
        });
    </script>
</body>
</html>