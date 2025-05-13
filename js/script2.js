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
    const productoAncho = productos?.[0]?.offsetWidth + 16 || 0; // Ancho de un producto + margen

    if (!carrusel || !prevButton || !nextButton || !indicatorsContainer || !pausePlayButton) {
        console.error('Elementos del carrusel no encontrados.');
        return;
    }

    // Inicializar indicadores
    const indicators = [];
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
        indicators.push(indicator);
    }

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
        currentIndex = (currentIndex + 1) % numProductos; // Esta línea asegura el reinicio
        scrollToIndex(currentIndex);
        updateIndicators();
    };

    const scrollToPrev = () => {
        currentIndex = (currentIndex - 1 + numProductos) % numProductos; // Esta línea asegura el reinicio
        scrollToIndex(currentIndex);
        updateIndicators();
    };

    // Deslizamiento automático
    const startAutoSlide = () => {
        autoSlideInterval = setInterval(scrollToNext, 3000);
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

    // Inicializar
    scrollToIndex(currentIndex);
    updateIndicators();
    startAutoSlide();

    // Event listeners para los botones
    prevButton.addEventListener('click', () => {
        scrollToPrev();
        restartAutoSlide();
    });

    nextButton.addEventListener('click', () => {
        scrollToNext();
        restartAutoSlide();
    });

    // Pausa / Play
    pausePlayButton.addEventListener('click', () => {
        if (isAutoSliding) {
            stopAutoSlide();
        } else {
            startAutoSlide();
        }
    });

    // Drag events
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
        const walk = (x - startX) * 1; //scroll speed
        carrusel.scrollLeft = scrollLeft - walk;
    });

    carruselContainer.addEventListener('mouseup', () => {
        isDragging = false;
        carruselContainer.classList.remove('cursor-grab');
        restartAutoSlide();
    });

    carruselContainer.addEventListener('mouseleave', () => {
        isDragging = false;
        carruselContainer.classList.remove('cursor-grab');
        restartAutoSlide();
    });

    // Opcional: Efecto "peek"
    if (carruselContainer && productoAncho > 0) {
        const peekAmount = (carruselContainer.offsetWidth - productoAncho) / 2;
        carruselContainer.style.paddingLeft = `${peekAmount}px`;
        carruselContainer.style.paddingRight = `${peekAmount}px`;
        carrusel.style.marginLeft = `-${peekAmount}px`;
        carrusel.style.marginRight = `-${peekAmount}px`;
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const mainImageContainer = document.getElementById('main-image-container');
    const mainImage = document.getElementById('main-image');
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const closeLightboxButton = document.getElementById('close-lightbox');
    const prevImageButton = document.getElementById('prev-image');
    const nextImageButton = document.getElementById('next-image');
    const lightboxCounter = document.getElementById('lightbox-counter');
    const miniImages = document.querySelectorAll('.mini-image');

    if (!mainImageContainer || !lightbox || !lightboxImage || !closeLightboxButton ||
        !prevImageButton || !nextImageButton || !lightboxCounter) {
        console.error('Elementos del lightbox no encontrados.');
        return;
    }

    let lightboxImages = Array.from(miniImages).map(mini => mini.dataset.src);
    // Añade la imagen principal al inicio si no está ya en las miniaturas
    if (!lightboxImages.includes(mainImage.src)) {
        lightboxImages.unshift(mainImage.src);
    }

    const showLightboxImage = (index) => {
        if (index >= 0 && index < lightboxImages.length) {
            lightboxImage.src = lightboxImages[index];
            updateLightboxCounter(index + 1, lightboxImages.length);
            updateMiniImageActiveState(lightboxImages[index], miniImages);
        }
    };

    const updateLightboxCounter = (current, total) => {
        if (lightboxCounter) {
            lightboxCounter.textContent = `${current} / ${total}`;
        }
    };

    const updateMiniImageActiveState = (activeImageUrl, miniImages) => {
        miniImages.forEach(mini => {
            mini.classList.toggle('active', mini.dataset.src === activeImageUrl);
        });
    };

    let currentImageIndex = 0;

    // Eventos
    mainImageContainer.addEventListener('click', () => {
        currentImageIndex = lightboxImages.indexOf(mainImage.src);
        showLightboxImage(currentImageIndex);
        lightbox.classList.remove('hidden');
    });

    closeLightboxButton.addEventListener('click', () => lightbox.classList.add('hidden'));

    prevImageButton.addEventListener('click', () => {
        currentImageIndex = Math.max(currentImageIndex - 1, 0);
        showLightboxImage(currentImageIndex);
    });

    nextImageButton.addEventListener('click', () => {
        currentImageIndex = Math.min(currentImageIndex + 1, lightboxImages.length - 1);
        showLightboxImage(currentImageIndex);
    });

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            lightbox.classList.add('hidden');
        }
    });

    miniImages.forEach(mini => {
        mini.addEventListener('click', function () {
            const newImageSrc = this.dataset.src;
            mainImage.src = newImageSrc;
            updateMiniImageActiveState(newImageSrc, miniImages);
            currentImageIndex = lightboxImages.indexOf(newImageSrc);
            if (!lightbox.classList.contains('hidden')) {
                showLightboxImage(currentImageIndex);
            }
        });
    });

    // Inicializar estado activo de la miniatura
    updateMiniImageActiveState(mainImage.src, miniImages);
});

document.addEventListener('DOMContentLoaded', () => {
    const elementsToReveal = document.querySelectorAll('[data-reveal]');

    const revealElementsOnScroll = () => {
        elementsToReveal.forEach(element => {
            const topElement = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            const pointOfReveal = 150; // Ajusta este valor según sea necesario

            element.classList.toggle('reveal', topElement < windowHeight - pointOfReveal);
        });
    };

    window.addEventListener('load', revealElementsOnScroll);
    window.addEventListener('scroll', revealElementsOnScroll);
});

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('loaded');
});

document.addEventListener('DOMContentLoaded', () => {
    const registroForm = document.getElementById('registro-form');
    const mensajeRegistro = document.querySelector('.mensaje-registro');

    if (registroForm) {
        registroForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(registroForm);
            fetch('php/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor (Registro):', data);
                mensajeRegistro.innerHTML = '';
                mensajeRegistro.classList.remove('success');
                if (data.success) {
                    mensajeRegistro.classList.add('success');
                    mensajeRegistro.textContent = data.message;
                    registroForm.reset();
                } else if (data.message) {
                    mensajeRegistro.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error al enviar la solicitud de registro:', error);
                mensajeRegistro.textContent = 'Error al comunicarse con el servidor.';
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const mensajeLogin = document.querySelector('.mensaje-login');

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(loginForm);
            fetch('ruta/a/tu/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor (Login):', data);
                mensajeLogin.innerHTML = '';
                mensajeLogin.classList.remove('success');
                if (data.success) {
                    mensajeLogin.classList.add('success');
                    mensajeLogin.textContent = data.message;
                    window.location.href = '/dashboard.php'; // Reemplaza con la URL deseada
                } else if (data.message) {
                    mensajeLogin.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error al enviar la solicitud de inicio de sesión:', error);
                mensajeLogin.textContent = 'Error al comunicarse con el servidor.';
            });
        });
    }
});