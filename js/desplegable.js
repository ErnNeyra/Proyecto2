document.addEventListener('DOMContentLoaded', function () {
    // Referencias a los elementos principales del carrusel
    const outerCarouselWrapper = document.getElementById('productos-carrusel-wrapper');
    const carouselContainer = document.getElementById('productos-carrusel-container');
    const carousel = document.getElementById('productos-carrusel');
    const prevButton = document.getElementById('prev-producto');
    const nextButton = document.getElementById('next-producto');
    const indicatorsContainer = document.getElementById('carousel-indicators');

    // Verificar si faltan elementos HTML necesarios
    if (!outerCarouselWrapper || !carouselContainer || !carousel || !prevButton || !nextButton || !indicatorsContainer) {
        console.error('Error: Faltan elementos HTML necesarios para el carrusel.');
        if(outerCarouselWrapper) outerCarouselWrapper.style.display = 'none';
        return;
    }

    // Obtener los items originales (excluyendo clones)
    const originalItems = Array.from(carousel.children).filter(item => !item.classList.contains('carousel-clone'));
    const totalItems = originalItems.length;

    // Clonar todos los items originales para el loop infinito
    const numClones = totalItems;

    // Ocultar si no hay suficientes items para un loop suave
    if (totalItems < 3) {
         console.warn('No hay suficientes items para un carrusel con loop infinito suave.');
         outerCarouselWrapper.style.display = 'none';
         return;
    }

    // Variables de estado del carrusel
    let currentIndex = numClones; // Índice actual (inicialmente en el primer item original)
    let itemWidth = 0; // Ancho calculado de un item
    let gapValue = 0; // Valor del gap CSS
    let isTransitioning = false; // Bandera para evitar clics rápidos

    // --- Autoplay ---
    let autoPlayInterval = null;
    const autoPlayDelay = 5000; // Retraso del autoplay en ms
    let isPlaying = true; // Estado del autoplay

    function startAutoPlay() {
        if (isPlaying && !autoPlayInterval) {
            autoPlayInterval = setInterval(() => {
                moveCarouselRevised(1); // Mover al siguiente
            }, autoPlayDelay);
        }
    }

    function stopAutoPlay() {
        clearInterval(autoPlayInterval);
        autoPlayInterval = null;
    }

    function resetAutoPlay() {
        stopAutoPlay();
        if (isPlaying) {
            startAutoPlay();
        }
    }
    // --- Fin Autoplay ---

    // Calcula el ancho de un item (sin considerar el gap)
    function calculateItemWidthOffset() {
        if (originalItems.length > 0) {
            itemWidth = originalItems[0].offsetWidth;
        } else {
            itemWidth = 300; // Fallback
        }
    }

    // Obtiene el valor numérico del gap CSS
    function getCarouselGap() {
         const carouselStyle = getComputedStyle(carousel);
         gapValue = parseFloat(carouselStyle.gap) || 0;
    }

    // Configura el carrusel: clona items y posiciona
    function setupCarouselRevised() {
         carousel.querySelectorAll('.carousel-clone').forEach(clone => clone.remove());

         calculateItemWidthOffset(); // Calcula ancho item
         getCarouselGap(); // Obtiene gap

         // Clona items para el inicio y el final
         const clonesStart = originalItems.map(item => item.cloneNode(true));
         const clonesEnd = originalItems.map(item => item.cloneNode(true));

         clonesStart.forEach(clone => clone.classList.add('carousel-clone'));
         clonesEnd.forEach(clone => clone.classList.add('carousel-clone'));

         clonesStart.reverse().forEach(clone => carousel.insertBefore(clone, carousel.firstElementChild));
         clonesEnd.forEach(clone => carousel.appendChild(clone));

         currentIndex = numClones; // Posiciona en el primer original

         carousel.style.transition = 'none';
         // Posición inicial considerando item y gap
         carousel.style.transform = `translateX(${-currentIndex * (itemWidth + gapValue)}px)`;

         updateIndicators();
    }

    // Mueve el carrusel un paso
    function moveCarouselRevised(direction) {
         if (isTransitioning) return;
         isTransitioning = true;

         const targetIndex = currentIndex + direction;

         // Aplica transición
         carousel.style.transition = 'transform 0.8s ease-in-out';

         // Traslada considerando item y gap
         carousel.style.transform = `translateX(${-targetIndex * (itemWidth + gapValue)}px)`;

         // Lógica al finalizar la transición (loop)
         const handleTransitionEnd = () => {
             carousel.removeEventListener('transitionend', handleTransitionEnd);
             isTransitioning = false;
             currentIndex = targetIndex;

             // Salto instantáneo si llega a un clon de los extremos
             if (currentIndex === totalItems + numClones) { // Si llega al primer clon del final
                 currentIndex = numClones; // Salta al primer original
                 carousel.style.transition = 'none';
                 carousel.style.transform = `translateX(${-currentIndex * (itemWidth + gapValue)}px)`;
             } else if (currentIndex === numClones - 1) { // Si llega al último clon del inicio
                 currentIndex = totalItems + numClones - 1; // Salta al último original
                 carousel.style.transition = 'none';
                 carousel.style.transform = `translateX(${-currentIndex * (itemWidth + gapValue)}px)`;
             }

             updateIndicators();
         };

         carousel.addEventListener('transitionend', handleTransitionEnd);
    }

    // Actualiza el estado de los indicadores
    function updateIndicators() {
        indicatorsContainer.innerHTML = '';
        // Crea un indicador por cada item ORIGINAL (aunque estén ocultos por CSS)
        for (let i = 0; i < totalItems; i++) {
            const indicator = document.createElement('button');
            indicator.classList.add('carousel-indicator');
            indicator.setAttribute('data-index', i.toString());
            indicator.setAttribute('aria-label', `Ir al slide ${i + 1}`);

            indicator.addEventListener('click', () => {
                const targetOriginalIndex = parseInt(indicator.getAttribute('data-index'), 10);
                const targetCarouselIndex = targetOriginalIndex + numClones;

                if (currentIndex !== targetCarouselIndex) {
                    resetAutoPlay(); // Reinicia autoplay al hacer clic en indicador

                    isTransitioning = true;
                    carousel.style.transition = 'transform 0.8s ease-in-out';
                    carousel.style.transform = `translateX(${-targetCarouselIndex * (itemWidth + gapValue)}px)`;

                    const handleIndicatorTransitionEnd = () => {
                        carousel.removeEventListener('transitionend', handleIndicatorTransitionEnd);
                        isTransitioning = false;
                        currentIndex = targetCarouselIndex;
                        updateIndicators();
                    };
                    carousel.addEventListener('transitionend', handleIndicatorTransitionEnd);
                }
            });
            indicatorsContainer.appendChild(indicator);
        }

        // Marca el indicador activo (índice basado en items originales)
        let activeIndicatorIndex = (currentIndex - numClones + totalItems) % totalItems;

        if (indicatorsContainer.children.length > 0 && activeIndicatorIndex >= 0 && activeIndicatorIndex < indicatorsContainer.children.length) {
             indicatorsContainer.querySelectorAll('.carousel-indicator').forEach(indicator => indicator.classList.remove('active'));
            indicatorsContainer.children[activeIndicatorIndex].classList.add('active');
        }
    }


    // Event listeners para botones de navegación (llaman a la función de movimiento revisada)
    prevButton.addEventListener('click', () => {
        moveCarouselRevised(-1);
        resetAutoPlay();
    });
    nextButton.addEventListener('click', () => {
        moveCarouselRevised(1);
        resetAutoPlay();
    });

    // Pausa/Reinicia Autoplay con Hover
    outerCarouselWrapper.addEventListener('mouseenter', stopAutoPlay);
    outerCarouselWrapper.addEventListener('mouseleave', startAutoPlay);


    // Responsive: recalcular en cambio de tamaño
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            setupCarouselRevised(); // Reconfigura
             if (isPlaying) {
                 startAutoPlay(); // Reinicia autoplay si estaba activo
             }
        }, 250); // Debounce
    });


    // Inicialización del carrusel (al cargar la ventana completamente y en DOMContentLoaded como fallback)
    window.addEventListener('load', () => {
         setupCarouselRevised(); // Configura inicial
         startAutoPlay(); // Inicia autoplay
    });

    setupCarouselRevised(); // Fallback en DOMContentLoaded


    // --- Funcionalidad de Arrastre (Ratón/Táctil) ---
    let isDragging = false;
    let startPos = 0;
    let currentTranslate = 0;
    let prevTranslate = 0;
    let animationFrameId = null;

    // Actualiza la posición visual durante el arrastre
    function animateDrag() {
        carousel.style.transform = `translateX(${currentTranslate}px)`;
        if (isDragging) {
            animationFrameId = requestAnimationFrame(animateDrag);
        }
    }

    // Inicia arrastre con ratón
    carouselContainer.addEventListener('mousedown', (e) => {
         if (e.button !== 0) return;
         isDragging = true;
         startPos = e.clientX;
         const transformMatrix = getComputedStyle(carousel).getPropertyValue('transform');
         if (transformMatrix && transformMatrix !== 'none') {
              const matrix = transformMatrix.match(/matrix\((.+)\)/);
              if (matrix && matrix[1]) {
                  prevTranslate = parseFloat(matrix[1].split(', ')[4]);
              } else { prevTranslate = 0; }
         } else { prevTranslate = 0; }
         carousel.style.transition = 'none';
         carouselContainer.classList.add('dragging');
         stopAutoPlay(); // Pausa autoplay al arrastrar
         animationFrameId = requestAnimationFrame(animateDrag);
    });

    // Finaliza arrastre con ratón
    carouselContainer.addEventListener('mouseup', () => {
         if (!isDragging) return;
         cancelAnimationFrame(animationFrameId);
         isDragging = false;
         carouselContainer.classList.remove('dragging');

         const movedBy = currentTranslate - prevTranslate;
         const dragThreshold = itemWidth / 4; // Umbral para cambio de slide

         if (movedBy < -dragThreshold) { moveCarouselRevised(1); } // Swipe a la izquierda -> siguiente
         else if (movedBy > dragThreshold) { moveCarouselRevised(-1); } // Swipe a la derecha -> anterior
         else { // Volver a la posición anterior
             carousel.style.transition = 'transform 0.5s ease-in-out';
             carousel.style.transform = `translateX(${prevTranslate}px)`;
         }

         if (isPlaying) { startAutoPlay(); } // Reinicia autoplay
    });

    // Cancela arrastre si el ratón sale del contenedor
    carouselContainer.addEventListener('mouseleave', () => {
         if (isDragging) {
             cancelAnimationFrame(animationFrameId);
             isDragging = false;
             carouselContainer.classList.remove('dragging');
             carousel.style.transition = 'transform 0.5s ease-in-out';
             carousel.style.transform = `translateX(${prevTranslate}px)`;
             if (isPlaying) { startAutoPlay(); }
         }
    });

    // Sigue el movimiento del ratón durante el arrastre
    carouselContainer.addEventListener('mousemove', (e) => {
         if (!isDragging) return;
         e.preventDefault();
         const currentPosition = e.clientX;
         const diff = currentPosition - startPos;
         currentTranslate = prevTranslate + diff;
    }, { passive: true });


    // Inicia arrastre táctil
    carouselContainer.addEventListener('touchstart', (e) => {
        if (e.touches.length === 1) {
            isDragging = true;
            startPos = e.touches[0].clientX;
            const transformMatrix = getComputedStyle(carousel).getPropertyValue('transform');
             if (transformMatrix && transformMatrix !== 'none') {
                  const matrix = transformMatrix.match(/matrix\((.+)\)/);
                  if (matrix && matrix[1]) {
                      prevTranslate = parseFloat(matrix[1].split(', ')[4]);
                  } else { prevTranslate = 0; }
             } else { prevTranslate = 0; }
            carousel.style.transition = 'none';
            stopAutoPlay();
             animationFrameId = requestAnimationFrame(animateDrag);
        }
    }, { passive: true });

    // Finaliza arrastre táctil
    carouselContainer.addEventListener('touchend', () => {
         if (!isDragging) return;
         cancelAnimationFrame(animationFrameId);
         isDragging = false;

         const movedBy = currentTranslate - prevTranslate;
         const dragThreshold = itemWidth / 4; // Umbral para cambio de slide

         if (movedBy < -dragThreshold) { moveCarouselRevised(1); } // Swipe a la izquierda -> siguiente
         else if (movedBy > dragThreshold) { moveCarouselRevised(-1); } // Swipe a la derecha -> anterior
         else { // Volver a la posición anterior
             carousel.style.transition = 'transform 0.5s ease-in-out';
             carousel.style.transform = `translateX(${prevTranslate}px)`;
         }

         if (isPlaying) { startAutoPlay(); } // Reinicia autoplay
    });

    // Sigue el movimiento táctil durante el arrastre
    carouselContainer.addEventListener('touchmove', (e) => {
         if (!isDragging) return;
         if (e.touches.length === 1) {
             const currentPosition = e.touches[0].clientX;
             const diff = currentPosition - startPos;
             currentTranslate = prevTranslate + diff;
         }
    }, { passive: true });

});