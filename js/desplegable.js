document.addEventListener('DOMContentLoaded', function () {
    // Referencia al wrapper principal del carrusel (para eventos hover, etc.)
    const outerCarouselWrapper = document.getElementById('productos-carrusel-wrapper');
    // Referencia al contenedor interno con overflow hidden
    const carouselContainer = document.getElementById('productos-carrusel-container');
    // Referencia al contenedor de los items que se desplaza
    const carousel = document.getElementById('productos-carrusel');
    const prevButton = document.getElementById('prev-producto');
    const nextButton = document.getElementById('next-producto');
    const indicatorsContainer = document.getElementById('carousel-indicators');
    const pausePlayButton = document.getElementById('pause-play-button');
    const playIcon = document.getElementById('play-icon');
    const pauseIcon = document.getElementById('pause-icon');


    // Verificar que los elementos esenciales existan
    if (!outerCarouselWrapper || !carouselContainer || !carousel || !prevButton || !nextButton || !indicatorsContainer || !pausePlayButton || !playIcon || !pauseIcon) {
        console.warn('Faltan elementos necesarios para el carrusel. Verifique los IDs en su HTML.');
         // Opcional: ocultar el wrapper si faltan elementos
        if(outerCarouselWrapper) outerCarouselWrapper.style.display = 'none';
        return;
    }

    // Obtener los items originales (excluir clones si ya se han añadido antes por el loop infinito)
    const originalItems = Array.from(carousel.children).filter(item => !item.classList.contains('carousel-clone'));
    const totalItems = originalItems.length;
    const itemsVisibleLogic = 3;  // Items visibles en el carrusel (ajustar según diseño)

    // Ocultar los controles si no hay suficientes items
    if (totalItems < itemsVisibleLogic * 2 + 1) {
        prevButton.style.display = 'none';
        nextButton.style.display = 'none';
        indicatorsContainer.style.display = 'none';
        pausePlayButton.style.display = 'none'; // Ocultar el botón de pausa/play también
        return; // Detener la inicialización del carrusel
    }

    let currentIndex = 0;
    let itemWidth = 0;
    let isTransitioning = false;
    let autoPlayInterval = null;
    const autoPlayDelay = 3000; // Milisegundos entre cada desplazamiento
    let isPlaying = true; // Controlar si autoplay está activo

    function calculateItemWidth() {
        itemWidth = carouselContainer.clientWidth / itemsVisibleLogic; // Ancho basado en los items visibles
        originalItems.forEach(item => {
            item.style.width = `${itemWidth}px`;
        });
    }

    function setupCarousel() {
        calculateItemWidth();

        // Clona los primeros y últimos elementos para el loop infinito
        let firstClone = originalItems[0].cloneNode(true);
        let lastClone = originalItems[originalItems.length - 1].cloneNode(true);

        firstClone.classList.add('carousel-clone');
        lastClone.classList.add('carousel-clone');

        carousel.appendChild(firstClone);
        carousel.insertBefore(lastClone, originalItems[0]);

        // Recalcular el ancho después de añadir los clones
        calculateItemWidth();

        // Posicionar el carrusel inicialmente
        carousel.style.transform = `translateX(${-itemWidth * (1)}px)`; // Ajustado para el clon al inicio
    }

    function moveCarousel(direction) {
        if (isTransitioning) return;
        isTransitioning = true;

        currentIndex += direction;
        carousel.style.transition = 'transform 0.5s ease-in-out';
        carousel.style.transform = `translateX(${-itemWidth * (currentIndex + 1)}px)`; // Ajustado para clones

        const endTransition = () => {
            carousel.removeEventListener('transitionend', endTransition);
            if (currentIndex === originalItems.length) {
                currentIndex = 0;
                carousel.style.transition = 'none';
                carousel.style.transform = `translateX(${-itemWidth * (1)}px)`; // Vuelve al inicio
            }
            if (currentIndex === -1) {
                currentIndex = originalItems.length - 1;
                carousel.style.transition = 'none';
                carousel.style.transform = `translateX(${-itemWidth * (originalItems.length)}px)`; // Vuelve al final
            }
            isTransitioning = false;
        };

        carousel.addEventListener('transitionend', endTransition);
    }

    function updateIndicators() {
        indicatorsContainer.innerHTML = ''; // Limpiar indicadores anteriores
        for (let i = 0; i < originalItems.length; i++) {
            const indicator = document.createElement('button');
            indicator.classList.add('carousel-indicator');
            indicator.setAttribute('data-index', i.toString());
            indicator.addEventListener('click', () => {
                currentIndex = i;
                carousel.style.transition = 'transform 0.5s ease-in-out';
                carousel.style.transform = `translateX(${-itemWidth * (currentIndex + 1)}px)`;
                resetAutoPlay(); // Reiniciar autoplay al hacer clic en un indicador
            });
            indicatorsContainer.appendChild(indicator);
        }
        // Marcar el indicador activo inicial
        indicatorsContainer.children[currentIndex].classList.add('active');
    }

    function toggleAutoPlay() {
        if (isPlaying) {
            stopAutoPlay();
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        } else {
            startAutoPlay();
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
        }
        isPlaying = !isPlaying;
    }

    function startAutoPlay() {
        if (autoPlayInterval) return; // Evitar múltiples intervalos
        autoPlayInterval = setInterval(() => {
            moveCarousel(1);
        }, autoPlayDelay);
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
        autoPlayInterval = null;
    }

    // Event listeners
    prevButton.addEventListener('click', () => {
        moveCarousel(-1);
        resetAutoPlay(); // Reiniciar autoplay al interactuar
    });
    nextButton.addEventListener('click', () => {
        moveCarousel(1);
        resetAutoPlay(); // Reiniciar autoplay al interactuar
    });

    pausePlayButton.addEventListener('click', toggleAutoPlay);

    // Detener autoplay si el cursor está sobre el wrapper principal del carrusel
    outerCarouselWrapper.addEventListener('mouseenter', stopAutoPlay);
    outerCarouselWrapper.addEventListener('mouseleave', startAutoPlay);


    // Responsive: recalcular en cambio de tamaño de ventana
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            // Recalcular ancho del item y reconfigurar el carrusel
            setupCarousel(); // Vuelve a calcular el ancho, clona y posiciona
            updateIndicators();
            // Mantener el estado de autoplay si estaba activo
             if (isPlaying) {
                startAutoPlay();
            }
        }, 250); // Debounce
    });

    // Inicializar al cargar el DOM
    setupCarousel(); // Configurar clones, posición inicial e indicadores

     // Asegurar que el carrusel se posicione correctamente después de que todo cargue (ej: imágenes)
     window.addEventListener('load', () => {
         // Vuelve a configurar por si la carga de imágenes afectó el layout
         setupCarousel();
         updateIndicators();
     });

     // Función auxiliar para reiniciar el autoplay
     function resetAutoPlay() {
         if (isPlaying) {
             startAutoPlay();
         }
     }

});