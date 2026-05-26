document.addEventListener('DOMContentLoaded', () => {
    // Seleccionamos todos los botones de "Marcar como Completado"
    const botones = document.querySelectorAll('.btn-completar');

    botones.forEach(boton => {
        boton.addEventListener('click', async function() {
            // Prevenimos múltiples clics desactivando el botón temporalmente
            this.disabled = true;
            this.textContent = 'Guardando...';
            
            const idDetalle = this.getAttribute('data-id');

            try {
                // Hacemos la petición POST al controlador
                // Nota: Aquí asumo que tienes un index.php que rutea las peticiones, 
                // ajusta la URL según cómo configures tu Front Controller.
                const respuesta = await fetch('/greenland/index.php?action=completar_visita', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_detalle: idDetalle })
                });

                const data = await respuesta.json();

                if (data.success) {
                    // Actualizamos la interfaz visualmente sin recargar
                    const badge = document.getElementById(`badge-${idDetalle}`);
                    badge.textContent = 'completado';
                    badge.classList.remove('pendiente');
                    badge.classList.add('completado');

                    // Dejamos el botón desactivado permanentemente con nuevo texto
                    this.textContent = 'Servicio Finalizado';
                } else {
                    alert('Hubo un error al guardar. Intenta de nuevo.');
                    this.disabled = false;
                    this.textContent = 'Marcar como Completado';
                }

            } catch (error) {
                console.error('Error en la red:', error);
                alert('Problema de conexión. Verifica tu internet.');
                this.disabled = false;
                this.textContent = 'Marcar como Completado';
            }
        });
    });
});