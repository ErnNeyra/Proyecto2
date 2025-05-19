document.addEventListener('DOMContentLoaded', () => {
    const carouselContainer = document.getElementById('carrusel-relacionados-container');
    const carousel = document.getElementById('carrusel-relacionados');
    const prevButton = document.getElementById('prev-relacionado');
    const nextButton = document.getElementById('next-relacionado');

    // Verificar que los elementos existan
    if (!carouselContainer || !carousel || !prevButton || !nextButton) {
        // console.warn('Elementos del carrusel de relacionados no encontrados.');
        return; // Salir si los elementos no están presentes (ej: si no hay relacionados)
    }

    // Función para desplazar el carrusel
    function scrollCarousel(direction) {
        // Determinar el ancho de un item + margen (aproximado)
        const itemWidth = carousel.firstElementChild ? carousel.firstElementChild.offsetWidth + 16 : 300; // 16px es un estimado del mr-4
        const scrollDistance = itemWidth * direction;
        carouselContainer.scrollBy({
            left: scrollDistance,
            behavior: 'smooth' // Desplazamiento suave
        });
    }

    // Event listeners para los botones
    prevButton.addEventListener('click', () => {
        scrollCarousel(-1); // Desplazar hacia la izquierda
    });

    nextButton.addEventListener('click', () => {
        scrollCarousel(1); // Desplazar hacia la derecha
    });
});