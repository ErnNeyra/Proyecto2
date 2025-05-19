document.addEventListener('DOMContentLoaded', () => {
    const carouselContainer = document.getElementById('carrusel-relacionados-container');
    const carousel = document.getElementById('carrusel-relacionados');
    const prevButton = document.getElementById('prev-relacionado');
    const nextButton = document.getElementById('next-relacionado');

    // Verificar que los elementos existan
    if (!carouselContainer || !carousel || !prevButton || !nextButton) {
       
        return; // Salir si los elementos no están presentes
    }

    // Función para desplazar el carrusel
    function scrollCarousel(direction) {
      
        const itemWidth = carousel.firstElementChild ? carousel.firstElementChild.offsetWidth + 16 : 300; // Fallback a 300px si no hay items
        const scrollDistance = itemWidth * direction;

        // Desplazamiento suave
        carouselContainer.scrollBy({
            left: scrollDistance,
            behavior: 'smooth'
        });
    }

    // Event listeners para los botones de navegación
    prevButton.addEventListener('click', () => {
        scrollCarousel(-1); // Desplazar hacia la izquierda
    });

    nextButton.addEventListener('click', () => {
        scrollCarousel(1); // Desplazar hacia la derecha
    });

   
});