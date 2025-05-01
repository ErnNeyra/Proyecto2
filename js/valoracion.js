document.addEventListener('DOMContentLoaded', () => {
    const productoValoraciones = document.querySelectorAll('.bg-white'); // Selecciona cada contenedor de producto

    productoValoraciones.forEach(producto => {
        const starButtons = producto.querySelectorAll('.star-button');
        const valoracionSpan = producto.querySelector('.flex span.text-gray-600');
        let currentRating = 0;
        let numValoraciones = 0;

        starButtons.forEach(button => {
            button.addEventListener('click', function() {
                const value = parseInt(this.dataset.value);
                currentRating = value;
                numValoraciones++;
                updateStars(starButtons, currentRating);
                if (valoracionSpan) {
                    valoracionSpan.textContent = `(${numValoraciones} valoraciones)`;
                }
                // Aquí iría la lógica para enviar la valoración al servidor
                console.log(`Producto valorado con ${currentRating} estrellas.`);
            });
        });
    });

    function updateStars(buttons, rating) {
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
});