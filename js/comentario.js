document.addEventListener('DOMContentLoaded', () => {
    // Elementos del formulario de comentario
    const starButtonsComentario = document.querySelectorAll('.star-button-comentario');
    const inputValoracionComentario = document.getElementById('valoracion-comentario');
    const formularioComentario = document.getElementById('formulario-comentario');
    const textareaComentario = document.getElementById('comentario');
    const listaComentarios = document.getElementById('lista-comentarios'); // Contenedor donde se muestran los comentarios
    const feedbackMensaje = document.getElementById('feedback-mensaje'); // Div para mensajes al usuario

    let currentRatingComentario = 0;

    // Lógica para seleccionar las estrellas de valoración en el formulario
    starButtonsComentario.forEach(button => {
        button.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            currentRatingComentario = value;
            inputValoracionComentario.value = currentRatingComentario;
            updateStarsComentario(starButtonsComentario, currentRatingComentario);
        });
    });

    // Función para actualizar visualmente las estrellas
    function updateStarsComentario(buttons, rating) {
        buttons.forEach(button => {
            const value = parseInt(button.dataset.value);
            const starIcon = button.querySelector('i');

            if (starIcon) {
                 if (value <= rating) {
                     starIcon.classList.remove('far', 'text-gray-300');
                     starIcon.classList.add('fas', 'text-yellow-500');
                 } else {
                     starIcon.classList.remove('fas', 'text-yellow-500');
                     starIcon.classList.add('far', 'text-gray-300');
                 }
             } else {
                 if (value <= rating) {
                     button.classList.remove('text-gray-300');
                     button.classList.add('text-yellow-500');
                 } else {
                     button.classList.remove('text-yellow-500');
                     button.classList.add('text-gray-300');
                 }
             }
        });
    }

    // Lógica para enviar el formulario de comentario y valoración via AJAX
    if (formularioComentario) {
        formularioComentario.addEventListener('submit', function(event) {
            event.preventDefault();

            // Validaciones mínimas del lado del cliente
            if (currentRatingComentario === 0) {
                mostrarFeedback('Por favor, selecciona una valoración.', 'error');
                return;
            }
            if (textareaComentario.value.trim() === '') {
                mostrarFeedback('Por favor, escribe un comentario.', 'error');
                return;
            }

            // Obtener la URL de envío desde el atributo data del formulario
            const submitUrl = formularioComentario.dataset.submitUrl;

            // Verificar si la URL de envío existe
            if (!submitUrl) {
                console.error('ERROR: No se especificó la URL de envío en el formulario (data-submit-url).');
                mostrarFeedback('Error de configuración del formulario.', 'error');
                return;
            }

            // Recoger los datos del formulario (FormData recoge automáticamente todos los campos)
            const formData = new FormData(formularioComentario);
            // formData.append('id_servicio', idServicio); // Ya no necesitamos obtener el ID manualmente, FormData lo hace
            // formData.append('valoracion', valoracion); // Ya no necesitamos obtener el valoración manualmente, FormData lo hace
            // formData.append('comentario', comentario); // Ya no necesitamos obtener el comentario manualmente, FormData lo hace

            mostrarFeedback('Publicando comentario...', 'info');
            const submitButton = formularioComentario.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            // Enviar los datos al script PHP usando fetch API, usando la URL obtenida
            fetch(submitUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new TypeError('Respuesta del servidor no es JSON. Posible error de PHP. Revisa los logs del servidor.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    mostrarFeedback(data.message, 'success');

                    // Limpiar el formulario
                    updateStarsComentario(starButtonsComentario, 0);
                    textareaComentario.value = '';
                    currentRatingComentario = 0;
                    inputValoracionComentario.value = 0;

                    // --- Añadir el nuevo comentario a la lista dinámicamente ---
                    if (data.comentario) {
                        const nuevoComentarioHTML = crearHTMLComentario(data.comentario); // Usa la función sin foto de perfil
                        listaComentarios.prepend(nuevoComentarioHTML); // Añade al inicio
                         // Opcional: Aquí podrías añadir lógica para actualizar la valoración promedio mostrada si el servidor devolviera el nuevo promedio
                    }

                } else {
                    mostrarFeedback('Error al publicar: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud AJAX:', error);
                mostrarFeedback('Error de comunicación con el servidor. Inténtalo de nuevo.', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
            });
        });
    }

    // Función para mostrar mensajes de feedback al usuario
    function mostrarFeedback(message, type) {
        if (feedbackMensaje) {
            feedbackMensaje.textContent = message;
             // Limpiar clases existentes y añadir las base de Tailwind
             feedbackMensaje.className = 'mt-4 text-sm font-semibold';
            if (type === 'success') {
                feedbackMensaje.classList.add('text-green-600');
            } else if (type === 'error') {
                feedbackMensaje.classList.add('text-red-600');
            } else if (type === 'info') {
                 feedbackMensaje.classList.add('text-blue-600');
            }
             if (type !== 'info') {
                 setTimeout(() => {
                     feedbackMensaje.textContent = '';
                     feedbackMensaje.className = 'mt-4 text-sm font-semibold'; // Mantener clases base después de ocultar
                 }, 5000);
             }
        }
    }

    // Función para crear el elemento HTML de un comentario (Sin foto de perfil)
    // Esta función es genérica y sirve para servicios y productos
    function crearHTMLComentario(comentarioData) {
        const comentarioDiv = document.createElement('div');
        comentarioDiv.classList.add('mb-6', 'p-4', 'bg-gray-100', 'rounded-md', 'border', 'border-gray-200', 'comentario-item');
        comentarioDiv.setAttribute('data-id', comentarioData.id_comentario);

        const fecha = new Date(comentarioData.fecha_creacion);
        let fechaFormateada = '';
        if (!isNaN(fecha.getTime())) {
            fechaFormateada = fecha.toLocaleDateString('es-ES', {
                 day: '2-digit',
                 month: '2-digit',
                 year: 'numeric',
                 hour: '2-digit',
                 minute: '2-digit'
            });
        } else {
            fechaFormateada = 'Fecha desconocida';
             // console.warn('Fecha inválida recibida para comentario:', comentarioData.fecha_creacion); // Opcional: log en consola
        }


        let estrellasHTML = '';
         const valoracionIndividual = comentarioData.valoracion ?? 0;
         for (let i = 1; i <= 5; i++) {
             if (i <= valoracionIndividual) {
                 estrellasHTML += '<i class="fas fa-star text-yellow-500 text-sm mr-1"></i>';
             } else {
                 estrellasHTML += '<i class="far fa-star text-gray-300 text-sm mr-1"></i>';
             }
         }

        comentarioDiv.innerHTML = `
            <div class="flex items-center mb-2">
                <p class="text-gray-700 font-semibold mr-2">${htmlspecialchars(comentarioData.nombre_usuario_comentario)}:</p>
                 <span class="text-gray-600 text-sm">${fechaFormateada}</span>
            </div>
            <p class="text-gray-700 mb-2">${htmlspecialchars(comentarioData.comentario)}</p>
            <div class="flex items-center">
                ${estrellasHTML}
            </div>
        `;

        return comentarioDiv;
    }

    function htmlspecialchars(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&#039;');
    }

});