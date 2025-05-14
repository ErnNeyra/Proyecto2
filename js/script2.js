document.addEventListener('DOMContentLoaded', () => {

    // --- Carrusel de Productos ---
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
    // Asegúrate de que el ancho del producto se calcule correctamente, incluyendo el margen.
    // Una forma más robusta es obtener el primer elemento y su ancho + margen.
    let productoAncho = 0;
    if (productos && productos.length > 0) {
        const primerProducto = productos[0];
        const estiloComputado = getComputedStyle(primerProducto);
        productoAncho = primerProducto.offsetWidth + parseFloat(estiloComputado.marginRight);
    }


    if (carrusel && prevButton && nextButton && indicatorsContainer && pausePlayButton && numProductos > 0) {
        // Inicializar indicadores
        const indicators = [];
        indicatorsContainer.innerHTML = ''; // Limpiar indicadores existentes si los hay
        for (let i = 0; i < numProductos; i++) {
            const indicator = document.createElement('button');
            // Usamos tus clases CSS personalizadas para los indicadores
            indicator.classList.add('carrusel-indicador');
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
                indicator.classList.toggle('activo', index === currentIndex);
            });
        };

        const scrollToIndex = (index) => {
             if (carrusel) {
                carrusel.style.transform = `translateX(-${index * productoAncho}px)`;
             }
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
            if (autoSlideInterval) clearInterval(autoSlideInterval); // Evita múltiples intervalos
            autoSlideInterval = setInterval(scrollToNext, 4000); // Intervalo de 4 segundos
            isAutoSliding = true;
            if (playIcon && pauseIcon) {
                playIcon.style.display = 'none';
                pauseIcon.style.display = 'inline-block';
            }
        };

        const stopAutoSlide = () => {
            clearInterval(autoSlideInterval);
            isAutoSliding = false;
             if (playIcon && pauseIcon) {
                playIcon.style.display = 'inline-block';
                pauseIcon.style.display = 'none';
             }
        };

        const restartAutoSlide = () => {
            stopAutoSlide();
            startAutoSlide();
        };

        // Inicializar
        scrollToIndex(currentIndex);
        updateIndicators();
        startAutoSlide(); // Iniciar el auto-slide al cargar

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

        // Drag events (Mejorado con detección de movimiento)
        carruselContainer.addEventListener('mousedown', (e) => {
             isDragging = true;
             // Guardamos la posición inicial del ratón y el scroll actual del carrusel
             startX = e.pageX;
             scrollLeft = carrusel.scrollLeft;
             stopAutoSlide();
             carruselContainer.classList.add('cursor-grabbing'); // Cambia el cursor
             carrusel.style.scrollBehavior = 'auto'; // Desactiva el scroll suave temporalmente
        });

        carruselContainer.addEventListener('mouseleave', () => {
            if (isDragging) {
                 isDragging = false;
                 carruselContainer.classList.remove('cursor-grabbing');
                 carrusel.style.scrollBehavior = 'smooth'; // Reactiva el scroll suave
                 // Al soltar el ratón, ajustamos a la tarjeta más cercana si es necesario
                 // (La lógica de ajustar a la tarjeta más cercana puede ser más compleja)
                 restartAutoSlide();
            }
        });

        carruselContainer.addEventListener('mouseup', () => {
             if (isDragging) {
                isDragging = false;
                carruselContainer.classList.remove('cursor-grabbing');
                carrusel.style.scrollBehavior = 'smooth'; // Reactiva el scroll suave
                // Al soltar el ratón, ajustamos a la tarjeta más cercana si es necesario
                // (La lógica de ajustar a la tarjeta más cercana puede ser más compleja)
                restartAutoSlide();
            }
        });

        carruselContainer.addEventListener('mousemove', (e) => {
            if (!isDragging || !carrusel) return;
            e.preventDefault(); // Previene la selección de texto
            const x = e.pageX;
            const walk = (x - startX) * 1.5; // Factor para ajustar la velocidad del scroll
             carrusel.scrollLeft = scrollLeft - walk;
        });

        // Opcional: Efecto "peek" (requiere que el contenedor tenga overflow: hidden)
        // Este efecto de padding puede interferir con el calculo de ancho del carrusel,
        // a veces es mejor manejarlo solo con CSS o con un enfoque JS diferente.
        // Si lo mantienes, asegúrate de que productoAncho lo tenga en cuenta.
        /*
        if (carruselContainer && productoAncho > 0) {
            const peekAmount = (carruselContainer.offsetWidth - productoAncho) / 2;
            carruselContainer.style.paddingLeft = `${peekAmount}px`;
            carruselContainer.style.paddingRight = `${peekAmount}px`;
             // Asegúrate de que el carrusel mismo no tenga márgenes que sumen a este padding
            carrusel.style.marginLeft = `-${peekAmount}px`;
            carrusel.style.marginRight = `-${peekAmount}px`;
        }
        */

    } else {
         console.warn('Carrusel de productos no inicializado. Asegúrate de que los elementos existen y hay productos.');
          // Ocultar controles si no hay productos
          if(carruselContainer) carruselContainer.style.display = 'none';
    }


    // --- Lightbox de Imágenes (Mantienes tu implementación) ---
    const mainImageContainer = document.getElementById('main-image-container'); // Asegúrate de que exista en detalleProducto.php
    const mainImage = document.getElementById('main-image'); // Asegúrate de que exista en detalleProducto.php
    const lightbox = document.getElementById('lightbox'); // Asegúrate de que exista en detalleProducto.php
    const lightboxImage = document.getElementById('lightbox-image'); // Asegúrate de que exista en detalleProducto.php
    const closeLightboxButton = document.getElementById('close-lightbox'); // Asegúrate de que exista en detalleProducto.php
    const prevImageButton = document.getElementById('prev-image'); // Asegúrate de que exista en detalleProducto.php
    const nextImageButton = document.getElementById('next-image'); // Asegúrate de que exista en detalleProducto.php
    const lightboxCounter = document.getElementById('lightbox-counter'); // Asegúrate de que exista en detalleProducto.php
    const miniImages = document.querySelectorAll('.mini-image'); // Asegúrate de que existan en detalleProducto.php


    if (mainImageContainer && lightbox && lightboxImage && closeLightboxButton &&
        prevImageButton && nextImageButton && lightboxCounter && miniImages.length > 0) {

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
                 // Habilitar/deshabilitar botones de navegación del lightbox
                prevImageButton.disabled = index === 0;
                nextImageButton.disabled = index === lightboxImages.length - 1;
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
            lightbox.classList.remove('hidden'); // Usa hidden como en Tailwind
            document.body.classList.add('overflow-hidden'); // Evita scroll del body
        });

        closeLightboxButton.addEventListener('click', () => {
             lightbox.classList.add('hidden');
             document.body.classList.remove('overflow-hidden');
        });


        prevImageButton.addEventListener('click', () => {
            if (currentImageIndex > 0) {
                 currentImageIndex = currentImageIndex - 1;
                 showLightboxImage(currentImageIndex);
            }
        });

        nextImageButton.addEventListener('click', () => {
             if (currentImageIndex < lightboxImages.length - 1) {
                currentImageIndex = currentImageIndex + 1;
                showLightboxImage(currentImageIndex);
             }
        });

        // Cerrar lightbox al clickear fuera de la imagen
        lightbox.addEventListener('click', (event) => {
            // Si el click fue directamente en el contenedor del lightbox y no en la imagen o controles
            if (event.target === lightbox) {
                 lightbox.classList.add('hidden');
                 document.body.classList.remove('overflow-hidden');
            }
        });

         // Navegación con teclado para el lightbox
        document.addEventListener('keydown', function(event) {
            if (!lightbox.classList.contains('hidden')) { // Solo si el lightbox está visible
                 if (event.key === 'ArrowLeft') {
                    prevImageButton.click(); // Simula click en botón previo
                 } else if (event.key === 'ArrowRight') {
                    nextImageButton.click(); // Simula click en botón siguiente
                 } else if (event.key === 'Escape') {
                     closeLightboxButton.click(); // Simula click en botón cerrar
                 }
            }
        });


        miniImages.forEach(mini => {
            mini.addEventListener('click', function () {
                const newImageSrc = this.dataset.src;
                mainImage.src = newImageSrc;
                updateMiniImageActiveState(newImageSrc, miniImages);
                currentImageIndex = lightboxImages.indexOf(newImageSrc);
                if (!lightbox.classList.contains('hidden')) { // Si el lightbox está abierto, actualiza la imagen también
                    showLightboxImage(currentImageIndex);
                }
            });
        });

        // Inicializar estado activo de la miniatura al cargar
        updateMiniImageActiveState(mainImage.src, miniImages);

    } else {
         console.warn('Elementos del lightbox no encontrados o no hay miniaturas.');
    }


    // --- Animaciones al Scroll (Mantienes tu implementación) ---
    const elementsToReveal = document.querySelectorAll('[data-reveal], .js-fade-in-up'); // Añadimos la clase .js-fade-in-up

    const revealElementsOnScroll = () => {
        elementsToReveal.forEach(element => {
            const topElement = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            const pointOfReveal = element.dataset.revealOffset || 150; // Usa un data attribute para offset o 150px por defecto

            // Añade la clase 'reveal' si el elemento está en el rango visible
            if (topElement < windowHeight - pointOfReveal && topElement > -element.offsetHeight) {
                element.classList.add('reveal');
            } else {
                 // Opcional: Quitar la clase 'reveal' si sale del viewport por arriba (para re-animar al volver)
                 // if (topElement > windowHeight || topElement < -element.offsetHeight) {
                 //     element.classList.remove('reveal');
                 // }
            }
        });
    };

    // Usar un IntersectionObserver para un rendimiento más eficiente
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal');
                    // Si quieres animar solo una vez, puedes dejar de observar
                    // observer.unobserve(entry.target);
                } else {
                     // Opcional: Quitar la clase si sale (para re-animar)
                     // entry.target.classList.remove('reveal');
                }
            });
        }, {
            rootMargin: '0px 0px -10% 0px', // Empieza a observar cuando falta un 10% para llegar
            threshold: 0.1 // Al menos el 10% del elemento debe ser visible
        });

        elementsToReveal.forEach(element => {
            observer.observe(element);
        });

         // Ejecutar revealElementsOnScroll una vez al cargar para elementos ya visibles
         revealElementsOnScroll(); // Esto atrapará los elementos en la parte superior de la página

    } else {
         // Fallback para navegadores antiguos
         window.addEventListener('scroll', revealElementsOnScroll);
         window.addEventListener('load', revealElementsOnScroll); // También al cargar
         revealElementsOnScroll(); // Ejecutar al inicio
    }


    // --- Loader (Mantienes tu implementación si lo usas) ---
    // document.body.classList.add('loaded'); // Añade esta clase al body cuando la página esté lista.
                                            // Necesitarías CSS para animar basado en esta clase.

    // --- Formulario de Registro (Mantienes tu implementación) ---
    const registroForm = document.getElementById('registro-form'); // Asegúrate de que exista
    const mensajeRegistro = document.querySelector('.mensaje-registro'); // Asegúrate de que exista

    if (registroForm && mensajeRegistro) {
        registroForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(registroForm);
            // Asegúrate de que la ruta a tu script PHP sea correcta
            fetch('php/login.php', { // O la ruta correcta para el registro
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor (Registro):', data);
                mensajeRegistro.innerHTML = '';
                mensajeRegistro.classList.remove('success', 'form__error'); // Limpia clases anteriores
                if (data.success) {
                    mensajeRegistro.classList.add('success');
                    mensajeRegistro.textContent = data.message;
                    registroForm.reset();
                } else if (data.message) {
                    mensajeRegistro.classList.add('form__error'); // Usa tu clase de error CSS
                    mensajeRegistro.textContent = data.message;
                } else {
                     mensajeRegistro.classList.add('form__error');
                     mensajeRegistro.textContent = 'Ocurrió un error en el registro.';
                }
            })
            .catch(error => {
                console.error('Error al enviar la solicitud de registro:', error);
                mensajeRegistro.classList.add('form__error');
                mensajeRegistro.textContent = 'Error al comunicarse con el servidor.';
            });
        });
    } else {
        console.warn('Formulario de registro o mensaje no encontrados.');
    }

    // --- Formulario de Login (Mantienes tu implementación) ---
    const loginForm = document.getElementById('login-form'); // Asegúrate de que exista
    const mensajeLogin = document.querySelector('.mensaje-login'); // Asegúrate de que exista

    if (loginForm && mensajeLogin) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(loginForm);
            // Asegúrate de que la ruta a tu script PHP de login sea correcta
            fetch('ruta/a/tu/login.php', { // Ajusta esta ruta
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor (Login):', data);
                mensajeLogin.innerHTML = '';
                mensajeLogin.classList.remove('success', 'form__error'); // Limpia clases anteriores
                if (data.success) {
                    mensajeLogin.classList.add('success');
                    mensajeLogin.textContent = data.message;
                    // Redirigir solo si el login es exitoso
                    window.location.href = data.redirect || '/panelUsuario.php'; // Usa data.redirect si tu PHP lo envía, sino por defecto a panelUsuario.php
                } else if (data.message) {
                    mensajeLogin.classList.add('form__error'); // Usa tu clase de error CSS
                    mensajeLogin.textContent = data.message;
                } else {
                     mensajeLogin.classList.add('form__error');
                     mensajeLogin.textContent = 'Ocurrió un error en el inicio de sesión.';
                }
            })
            .catch(error => {
                console.error('Error al enviar la solicitud de inicio de sesión:', error);
                 mensajeLogin.classList.add('form__error');
                mensajeLogin.textContent = 'Error al comunicarse con el servidor.';
            });
        });
    } else {
         console.warn('Formulario de login o mensaje no encontrados.');
    }

    // --- User Dropdown Toggle (Added) ---
    const userDropdownButton = document.getElementById('user-dropdown-button');
    const userDropdownMenu = document.getElementById('user-dropdown-menu');

    if (userDropdownButton && userDropdownMenu) {
        userDropdownButton.addEventListener('click', function(event) {
            event.stopPropagation(); // Evita que el click se propague al document
            // Toggle la clase 'hidden' para mostrar/ocultar el menú
            userDropdownMenu.classList.toggle('hidden');
            // Opcional: Toggle un atributo aria-expanded en el botón para accesibilidad
            // userDropdownButton.setAttribute('aria-expanded', !userDropdownMenu.classList.contains('hidden'));
        });

        // Cerrar el menú si se hace click fuera de él
        document.addEventListener('click', function(event) {
            // Si el menú no está oculto Y el click no fue dentro del panel del usuario
            const userPanel = userDropdownButton.parentElement;
            if (!userDropdownMenu.classList.contains('hidden') && userPanel && !userPanel.contains(event.target)) {
                 userDropdownMenu.classList.add('hidden');
                 // userDropdownButton.setAttribute('aria-expanded', 'false'); // Actualiza aria-expanded
            }
        });

        // Opcional: Cerrar el menú si se redimensiona la ventana
        window.addEventListener('resize', function() {
            if (!userDropdownMenu.classList.contains('hidden')) {
                userDropdownMenu.classList.add('hidden');
                // userDropdownButton.setAttribute('aria-expanded', 'false'); // Actualiza aria-expanded
            }
        });
    } else {
         console.log('Panel de usuario o menú desplegable no encontrados (quizás el usuario no está logueado).');
    }

    // --- Script para el header sticky y menú móvil (Movido desde index2.php) ---
    // Asegúrate de que estos selectores coincidan con tu HTML
    const header = document.querySelector('header');
    // const heroSection = document.querySelector('.hero-section'); // Si necesitas un offset basado en la sección


    const addHeaderShadow = () => {
        // Usa window.scrollY para detectar el desplazamiento
        if (window.scrollY > 50) { // Ajusta este valor según cuándo quieres que aparezca la sombra
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };

    // Añade el listener al scroll
    window.addEventListener('scroll', addHeaderShadow);

    // Ejecuta la función una vez al cargar
    addHeaderShadow();


    // --- Mobile Menu Toggle ---
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileNav = document.getElementById('mobile-nav');
    // const mainNav = document.getElementById('main-nav'); // No necesitas interactuar con main-nav directamente aquí

    if (mobileMenuButton && mobileNav) {
         mobileMenuButton.addEventListener('click', function () {
             // Toggle la visibilidad del menú móvil
             mobileNav.classList.toggle('hidden');

             // Opcional: cambia el icono del botón
             const icon = mobileMenuButton.querySelector('svg');
             if (mobileNav.classList.contains('hidden')) {
                 // Mostrar icono de hamburguesa
                 icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>';
             } else {
                 // Mostrar icono de cierre (una 'X')
                 icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
             }

             // Opcional: añade una clase al body para evitar el scroll
             document.body.classList.toggle('nav-mobile-open');
         });

         // Ocultar el menú móvil al hacer click en un enlace (opcional)
         mobileNav.querySelectorAll('a').forEach(link => {
             link.addEventListener('click', () => {
                 mobileNav.classList.add('hidden');
                 document.body.classList.remove('nav-mobile-open');
                 // Vuelve a cambiar el icono a hamburguesa si lo cambiaste al abrir
                 const icon = mobileMenuButton.querySelector('svg');
                  icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>';
             });
         });
    }


}); // Fin de DOMContentLoaded