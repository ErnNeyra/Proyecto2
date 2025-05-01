document.addEventListener('DOMContentLoaded', () => {
    const carruselContainer = document.getElementById('productos-carrusel-container');
    const carrusel = document.getElementById('productos-carrusel');
    const prevButton = document.getElementById('prev-producto');
    const nextButton = document.getElementById('next-producto');
    const indicatorsContainer = document.getElementById('carousel-indicators');
    const pausePlayButton = document.getElementById('pause-play-button');
    const playIcon = document.getElementById('play-icon');
    const pauseIcon = document.getElementById('pause-icon');
    const productos = carrusel?.children;
    const numProductos = productos?.length || 0;
    let currentIndex = 0;
    let autoSlideInterval;
    let isAutoSliding = true;
    let isDragging = false;
    let startX;
    let scrollLeft;

    if (!carrusel || !prevButton || !nextButton || !indicatorsContainer || !pausePlayButton) {
        return; // Si los elementos no existen, no ejecutar el script del carrusel
    }

    const productoAncho = productos[0]?.offsetWidth + 16 || 0; // Ancho de un producto + margen

    // Inicializar indicadores
    for (let i = 0; i < numProductos; i++) {
        const indicator = document.createElement('button');
        indicator.classList.add('w-3', 'h-3', 'rounded-full', 'bg-gray-400', 'focus:outline-none');
        indicator.dataset.index = i;
        indicator.addEventListener('click', () => {
            currentIndex = i;
            scrollToIndex(i);
            updateIndicators();
            restartAutoSlide();
        });
        indicatorsContainer.appendChild(indicator);
    }

    const indicators = indicatorsContainer.querySelectorAll('button');
    const updateIndicators = () => {
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('bg-yellow-500', index === currentIndex);
            indicator.classList.toggle('bg-gray-400', index !== currentIndex);
        });
    };

    const scrollToIndex = (index) => {
        carrusel.style.transform = `translateX(-${index * productoAncho}px)`;
    };

    const scrollToNext = () => {
        currentIndex = (currentIndex + 1) % numProductos;
        scrollToIndex(currentIndex);
        updateIndicators();
    };

    const scrollToPrev = () => {
        currentIndex = (currentIndex - 1 + numProductos) % numProductos;
        scrollToIndex(currentIndex);
        updateIndicators();
    };

    // Deslizamiento automático
    const startAutoSlide = () => {
        autoSlideInterval = setInterval(scrollToNext, 3000); // Cambia cada 3 segundos
        isAutoSliding = true;
        playIcon.classList.add('hidden');
        pauseIcon.classList.remove('hidden');
    };

    const stopAutoSlide = () => {
        clearInterval(autoSlideInterval);
        isAutoSliding = false;
        playIcon.classList.remove('hidden');
        pauseIcon.classList.add('hidden');
    };

    const restartAutoSlide = () => {
        stopAutoSlide();
        startAutoSlide();
    };

    // Inicializar el carrusel
    scrollToIndex(currentIndex);
    updateIndicators();
    startAutoSlide();

    // Eventos de los botones de navegación
    prevButton.addEventListener('click', () => {
        scrollToPrev();
        restartAutoSlide();
    });

    nextButton.addEventListener('click', () => {
        scrollToNext();
        restartAutoSlide();
    });

    // Pausa/Reproducción del automático
    pausePlayButton.addEventListener('click', () => {
        if (isAutoSliding) {
            stopAutoSlide();
        } else {
            startAutoSlide();
        }
    });

    // Deslizamiento con el ratón
    carruselContainer.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.pageX - carrusel.offsetLeft;
        scrollLeft = carrusel.scrollLeft;
        stopAutoSlide();
        carruselContainer.classList.add('cursor-grab');
    });

    carruselContainer.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        const x = e.pageX - carrusel.offsetLeft;
        const walk = (x - startX) * 1; // Ajustar la sensibilidad del arrastre
        carrusel.scrollLeft = scrollLeft - walk;
    });

    carruselContainer.addEventListener('mouseup', () => {
        isDragging = false;
        carruselContainer.classList.remove('cursor-grab');
        restartAutoSlide();
    });

    carruselContainer.addEventListener('mouseleave', () => {
        if (isDragging) {
            isDragging = false;
            carruselContainer.classList.remove('cursor-grab');
            restartAutoSlide();
        }
    });

    // Efecto de "peek" (opcional, requiere ajustar el CSS si no se ve bien)
    carruselContainer.style.paddingLeft = `${(carruselContainer.offsetWidth - productoAncho) / 2}px`;
    carruselContainer.style.paddingRight = `${(carruselContainer.offsetWidth - productoAncho) / 2}px`;
    carrusel.style.marginLeft = `-${(carruselContainer.offsetWidth - productoAncho) / 2}px`;
    carrusel.style.marginRight = `-${(carruselContainer.offsetWidth - productoAncho) / 2}px`;
});
document.addEventListener('DOMContentLoaded', () => {
    const contenedorImagenPrincipal = document.getElementById('contenedor-imagen-principal');
    const imagenPrincipal = document.getElementById('imagen-principal');
    const lightbox = document.getElementById('lightbox');
    const lightboxImagen = document.getElementById('lightbox-imagen');
    const cerrarLightboxButton = document.getElementById('cerrar-lightbox');
    const anteriorImagenButton = document.getElementById('anterior-imagen');
    const siguienteImagenButton = document.getElementById('siguiente-imagen');
    const lightboxIndicadores = document.getElementById('lightbox-indicadores');
    const miniImagenes = document.querySelectorAll('.mini-imagen');

    let listaImagenesLightbox = [];
    let indiceImagenActual = 0;

    // Obtener la lista de URLs de las imágenes grandes
    miniImagenes.forEach(miniatura => {
        listaImagenesLightbox.push(miniatura.dataset.src);
    });
    // Añadir también la imagen principal inicial a la lista si no está ya
    if (!listaImagenesLightbox.includes(imagenPrincipal.src)) {
        listaImagenesLightbox.unshift(imagenPrincipal.src);
    }

    function mostrarImagenLightbox(indice) {
        if (indice >= 0 && indice < listaImagenesLightbox.length) {
            lightboxImagen.src = listaImagenesLightbox[indice];
            actualizarIndicadoresLightbox(indice + 1, listaImagenesLightbox.length);
            actualizarMiniaturaActiva(listaImagenesLightbox[indice]);
        }
    }

    function actualizarIndicadoresLightbox(actual, total) {
        if (lightboxIndicadores) {
            lightboxIndicadores.textContent = `${actual} / ${total}`;
        }
    }

    function actualizarMiniaturaActiva(urlImagenActiva) {
        miniImagenes.forEach(miniatura => {
            miniatura.classList.remove('activa');
            if (miniatura.dataset.src === urlImagenActiva) {
                miniatura.classList.add('activa');
            }
        });
    }

    if (contenedorImagenPrincipal && lightbox && lightboxImagen && cerrarLightboxButton && anteriorImagenButton && siguienteImagenButton && lightboxIndicadores) {
        contenedorImagenPrincipal.addEventListener('click', () => {
            const srcPrincipal = imagenPrincipal.src;
            indiceImagenActual = listaImagenesLightbox.indexOf(srcPrincipal);
            mostrarImagenLightbox(indiceImagenActual);
            lightbox.classList.remove('hidden');
        });

        cerrarLightboxButton.addEventListener('click', () => {
            lightbox.classList.add('hidden');
        });

        anteriorImagenButton.addEventListener('click', () => {
            indiceImagenActual = Math.max(indiceImagenActual - 1, 0);
            mostrarImagenLightbox(indiceImagenActual);
        });

        siguienteImagenButton.addEventListener('click', () => {
            indiceImagenActual = Math.min(indiceImagenActual + 1, listaImagenesLightbox.length - 1);
            mostrarImagenLightbox(indiceImagenActual);
        });

        // Opcional: Cerrar el lightbox al hacer clic fuera de la imagen
        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                lightbox.classList.add('hidden');
            }
        });

        // Actualizar la imagen principal, la miniatura activa y, si el lightbox está abierto, su imagen e indicador también
        miniImagenes.forEach(miniatura => {
            miniatura.addEventListener('click', function() {
                const nuevaImagenSrc = this.dataset.src;
                imagenPrincipal.src = nuevaImagenSrc;
                actualizarMiniaturaActiva(nuevaImagenSrc);
                const nuevoIndice = listaImagenesLightbox.indexOf(nuevaImagenSrc);
                if (!lightbox.classList.contains('hidden')) {
                    indiceImagenActual = nuevoIndice;
                    mostrarImagenLightbox(indiceImagenActual);
                }
            });
        });

        // Inicializar la miniatura activa al cargar la página
        actualizarMiniaturaActiva(imagenPrincipal.src);
    }
});
document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('loaded');
});
function revealElements() {
    const revealElements = document.querySelectorAll('[data-reveal]');

    revealElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;

        // Ajusta este valor para controlar cuándo se activa la animación (ej: 150px antes de que el elemento llegue al borde inferior)
        const revealPoint = 150;

        if (elementTop < windowHeight - revealPoint) {
            element.classList.add('reveal');
        }
    });
}

// Llama a la función al cargar la página y al hacer scroll
window.addEventListener('load', revealElements);
window.addEventListener('scroll', revealElements);
document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('loaded');

    function revealElements() {
        const revealElements = document.querySelectorAll('[data-reveal]');
        revealElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            const revealPoint = 150;
            if (elementTop < windowHeight - revealPoint) {
                element.classList.add('reveal');
            }
        });
    }
    window.addEventListener('load', revealElements);
    window.addEventListener('scroll', revealElements);

    // Carrusel de productos
    const carruselContainer = document.getElementById('productos-carrusel-container');
    const carrusel = document.getElementById('productos-carrusel');
    const prevButton = document.getElementById('prev-producto');
    const nextButton = document.getElementById('next-producto');
    const indicadoresContainer = document.getElementById('carousel-indicators');
    const pausePlayButton = document.getElementById('pause-play-button');
    const playIcon = document.getElementById('play-icon');
    const pauseIcon = document.getElementById('pause-icon');

    if (carrusel && prevButton && nextButton && indicadoresContainer && pausePlayButton) {
        const productos = carrusel.children;
        const productoWidth = productos[0].offsetWidth + parseInt(getComputedStyle(productos[0]).marginRight);
        let currentIndex = 0;
        let isAutoplay = false;
        let autoplayInterval;

        function updateCarousel() {
            carrusel.style.transform = `translateX(-${currentIndex * productoWidth}px)`;
            updateIndicators();
        }

        function goToSlide(index) {
            currentIndex = Math.max(0, Math.min(index, productos.length - 1));
            updateCarousel();
        }

        function nextSlide() {
            goToSlide(currentIndex + 1);
        }

        function prevSlide() {
            goToSlide(currentIndex - 1);
        }

        function createIndicators() {
            for (let i = 0; i < productos.length; i++) {
                const indicator = document.createElement('button');
                indicator.classList.add('w-3', 'h-3', 'rounded-full', 'bg-gray-400', 'focus:outline-none');
                indicator.addEventListener('click', () => goToSlide(i));
                indicadoresContainer.appendChild(indicator);
            }
            updateIndicators();
        }

        function updateIndicators() {
            const indicators = indicadoresContainer.children;
            for (let i = 0; i < indicators.length; i++) {
                indicators[i].classList.toggle('bg-yellow-500', i === currentIndex);
                indicators[i].classList.toggle('bg-gray-400', i !== currentIndex);
            }
        }

        function startAutoplay() {
            isAutoplay = true;
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
            autoplayInterval = setInterval(nextSlide, 3000); // Cambia de slide cada 3 segundos
        }

        function stopAutoplay() {
            isAutoplay = false;
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
            clearInterval(autoplayInterval);
        }

        pausePlayButton.addEventListener('click', () => {
            if (isAutoplay) {
                stopAutoplay();
            } else {
                startAutoplay();
            }
        });

        prevButton.addEventListener('click', prevSlide);
        nextButton.addEventListener('click', nextSlide);

        createIndicators();
        // startAutoplay(); // Iniciar autoplay por defecto si lo deseas
    }
});
