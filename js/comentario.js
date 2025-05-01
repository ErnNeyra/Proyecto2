document.addEventListener('DOMContentLoaded', () => {
    const starButtonsComentario = document.querySelectorAll('.star-button-comentario');
    const inputValoracionComentario = document.getElementById('valoracion-comentario');
    const formularioComentario = document.getElementById('formulario-comentario');
    const textareaComentario = document.getElementById('comentario');

    let currentRatingComentario = 0;

    starButtonsComentario.forEach(button => {
        button.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            currentRatingComentario = value;
            inputValoracionComentario.value = currentRatingComentario;
            updateStarsComentario(starButtonsComentario, currentRatingComentario);
        });
    });

    function updateStarsComentario(buttons, rating) {
        buttons.forEach(button => {
            const value = parseInt(button.dataset.value);
            if (value <= rating) {
                button.classList.remove('text-gray-300');
                button.classList.add('text-yellow-500');
            } else {
                button.classList.remove('text-yellow-500');
                button.classList.add('text-gray-300');
            }
        });
    }

    if (formularioComentario) {
        formularioComentario.addEventListener('submit', function(event) {
            event.preventDefault(); // Evita la recarga de la página por ahora

            if (currentRatingComentario === 0) {
                alert('Por favor, selecciona una valoración.');
                return;
            }

            if (textareaComentario.value.trim() === '') {
                alert('Por favor, escribe un comentario.');
                return;
            }

            const valoracion = inputValoracionComentario.value;
            const comentario = textareaComentario.value;

            // Aquí iría la lógica para enviar la valoración y el comentario al servidor
            console.log('Valoración enviada:', valoracion);
            console.log('Comentario enviado:', comentario);

            // Opcional: Limpiar el formulario después de "publicar"
            updateStarsComentario(starButtonsComentario, 0);
            textareaComentario.value = '';
            currentRatingComentario = 0;
            inputValoracionComentario.value = 0;
        });
    }
});