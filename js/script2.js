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
 document.addEventListener('DOMContentLoaded', function() {
            const userDropdownButton = document.getElementById('user-dropdown-button');
            const userDropdown = document.getElementById('user-dropdown');

            if (userDropdownButton && userDropdown) {
                userDropdownButton.addEventListener('click', function() {
                    userDropdown.classList.toggle('hidden');
                    this.setAttribute('aria-expanded', !userDropdown.classList.contains('hidden'));
                });

                // Cerrar el desplegable si se hace clic fuera
                document.addEventListener('click', function(event) {
                    if (!userDropdownButton.contains(event.target) && !userDropdown.contains(event.target)) {
                        userDropdown.classList.add('hidden');
                        userDropdownButton.setAttribute('aria-expanded', false);
                    }
                });
            }
        });
           document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const precioInput = document.getElementById('precio');
        const nombreInput = document.getElementById('nombre');
        const descripcionInput = document.getElementById('descripcion');

        // Agregar div para mensajes de error antes del formulario
        const errorContainer = document.createElement('div');
        errorContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 hidden';
        errorContainer.setAttribute('role', 'alert');
        form.parentNode.insertBefore(errorContainer, form);

        form.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessages = [];

            // Validación del nombre
            if (nombreInput.value.length < 3 || nombreInput.value.length > 100) {
                errorMessages.push('El nombre debe tener entre 3 y 100 caracteres.');
                isValid = false;
            }

            // Validación de la descripción
            if (descripcionInput.value.length < 10 || descripcionInput.value.length > 500) {
                errorMessages.push('La descripción debe tener entre 10 y 500 caracteres.');
                isValid = false;
            }

            // Validación del precio
            if (precioInput.value <= 0) {
                errorMessages.push('El precio debe ser mayor que 0.');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                errorContainer.innerHTML = errorMessages.map(msg => `<p>${msg}</p>`).join('');
                errorContainer.classList.remove('hidden');
                // Scroll hacia el mensaje de error
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                errorContainer.classList.add('hidden');
            }
        });
    });
     const dropdownBtn = document.getElementById('dropdownBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });

            // Cerrar el desplegable si se hace clic fuera
            document.addEventListener('click', (event) => {
                if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }

        document.getElementById('mejorarDescripcion').addEventListener('click', async function() {
            const descripcion = document.getElementById('descripcion').value;
            const sugerenciaDiv = document.getElementById('sugerenciaDescripcion');
            const textoSugerencia = document.getElementById('textoSugerencia');

            if (!descripcion.trim()) {
                alert('Por favor, ingrese una descripción primero.');
                return;
            }

            try {
                this.disabled = true;
                this.textContent = 'Procesando...';

                const response = await fetch('util/mejorar_descripcion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ descripcion: descripcion })
                });

                const data = await response.json();

                if (data.success) {
                    textoSugerencia.textContent = data.success;
                    sugerenciaDiv.classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'No se pudo procesar la solicitud'));
                }
            } catch (error) {
                alert('Error al procesar la solicitud');
            } finally {
                this.disabled = false;
                this.textContent = 'Mejorar Descripción';
            }
        });

        document.getElementById('aplicarSugerencia').addEventListener('click', function() {
            const descripcion = document.getElementById('descripcion');
            const sugerencia = document.getElementById('textoSugerencia').textContent;
            descripcion.value = sugerencia;
            document.getElementById('sugerenciaDescripcion').classList.add('hidden');
        });

        // Funcionalidad para el generador de logos
        document.getElementById('generarLogo').addEventListener('click', function() {
            document.getElementById('modalGenerarLogo').classList.remove('hidden');
            document.getElementById('paso1').classList.remove('hidden');
            document.getElementById('paso2').classList.add('hidden');
            document.getElementById('logoDescripcion').value = '';
        });

        document.getElementById('cerrarModal').addEventListener('click', function() {
            document.getElementById('modalGenerarLogo').classList.add('hidden');
        });

        document.getElementById('generarLogoBtn').addEventListener('click', async function() {
            const descripcion = document.getElementById('logoDescripcion').value;
            const loadingIndicator = document.getElementById('loadingIndicator');

            if (!descripcion.trim()) {
                alert('Por favor, describe cómo quieres que sea tu logo.');
                return;
            }

            try {
                this.disabled = true;
                loadingIndicator.classList.remove('hidden');

                const response = await fetch('./util/generar_logo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ descripcion: descripcion })
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('logoGenerado').src = data.imageUrl;
                    document.getElementById('paso1').classList.add('hidden');
                    document.getElementById('paso2').classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'No se pudo generar el logo'));
                }
            } catch (error) {
                alert('Error al generar el logo');
            } finally {
                this.disabled = false;
                loadingIndicator.classList.add('hidden');
            }
        });

        document.getElementById('regenerarLogo').addEventListener('click', function() {
            document.getElementById('paso2').classList.add('hidden');
            document.getElementById('paso1').classList.remove('hidden');
        });

        document.getElementById('guardarLogo').addEventListener('click', async function() {
            const logoUrl = document.getElementById('logoGenerado').src;

            try {
                const response = await fetch(logoUrl);
                const blob = await response.blob();

                // Crear un objeto File
                const file = new File([blob], 'logo.png', { type: 'image/png' });

                // Crear un objeto DataTransfer y agregar el archivo
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                // Asignar el archivo al input de imagen
                document.getElementById('imagen').files = dataTransfer.files;

                // Cerrar el modal
                document.getElementById('modalGenerarLogo').classList.add('hidden');

                // Opcional: Mostrar mensaje de éxito
                alert('Logo agregado correctamente. No olvides guardar los cambios.');
            } catch (error) {
                alert('Error al guardar el logo: ' + error.message);
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
    const mejorarDescripcionBtn = document.getElementById('mejorarDescripcion');
    const sugerenciaDescripcionDiv = document.getElementById('sugerenciaDescripcion');
    const textoSugerenciaParrafo = document.getElementById('textoSugerencia');
    const aplicarSugerenciaBtn = document.getElementById('aplicarSugerencia');
    const descripcionTextarea = document.getElementById('descripcion');

    const generarLogoBtn = document.getElementById('generarLogo');
    const modalGenerarLogoDiv = document.getElementById('modalGenerarLogo');
    const cerrarModalBtn = document.getElementById('cerrarModal');
    const generarLogoModalBtn = document.getElementById('generarLogoBtn');
    const logoDescripcionTextarea = document.getElementById('logoDescripcion');
    const paso1Div = document.getElementById('paso1');
    const paso2Div = document.getElementById('paso2');
    const logoGeneradoImg = document.getElementById('logoGenerado');
    const loadingIndicatorDiv = document.getElementById('loadingIndicator');
    const guardarLogoBtn = document.getElementById('guardarLogo');
    const imagenInputFile = document.getElementById('imagen');

    if (mejorarDescripcionBtn) {
        mejorarDescripcionBtn.addEventListener('click', function() {
            // Aquí iría la lógica para llamar a una API o generar una sugerencia localmente
            const sugerencia = "Esta es una sugerencia para mejorar tu descripción. Intenta ser más específico sobre los beneficios que ofreces.";
            textoSugerenciaParrafo.textContent = sugerencia;
            sugerenciaDescripcionDiv.classList.remove('hidden');
        });
    }

    if (aplicarSugerenciaBtn) {
        aplicarSugerenciaBtn.addEventListener('click', function() {
            descripcionTextarea.value = textoSugerenciaParrafo.textContent;
            sugerenciaDescripcionDiv.classList.add('hidden');
        });
    }

    if (generarLogoBtn) {
        generarLogoBtn.addEventListener('click', function() {
            modalGenerarLogoDiv.classList.remove('hidden');
        });
    }

    if (cerrarModalBtn) {
        cerrarModalBtn.addEventListener('click', function() {
            modalGenerarLogoDiv.classList.add('hidden');
            paso1Div.classList.remove('hidden');
            paso2Div.classList.add('hidden');
            logoGeneradoImg.src = '';
        });
    }

    if (generarLogoModalBtn) {
        generarLogoModalBtn.addEventListener('click', function() {
            const descripcionLogo = logoDescripcionTextarea.value;
            if (descripcionLogo.trim() !== "") {
                loadingIndicatorDiv.classList.remove('hidden');
                paso1Div.classList.add('hidden');
                // Simulación de una llamada a una API de generación de logos
                setTimeout(function() {
                    const imageUrl = 'https://via.placeholder.com/150/4682B4/FFFFFF?Text=' + encodeURIComponent('Logo para ' + descripcionLogo);
                    logoGeneradoImg.src = imageUrl;
                    paso2Div.classList.remove('hidden');
                    loadingIndicatorDiv.classList.add('hidden');
                }, 2000);
            } else {
                alert("Por favor, describe cómo quieres tu logo.");
            }
        });
    }

    if (guardarLogoBtn) {
        guardarLogoBtn.addEventListener('click', function() {
            const logoUrl = logoGeneradoImg.src;
            // Aquí podrías implementar la lógica para descargar la imagen
            // o para mostrarla en el input file (esto es más complejo y puede requerir soluciones del lado del servidor).
            alert("La URL del logo generado es: " + logoUrl + ". Puedes hacer clic derecho y 'Guardar imagen como...' para descargarla y luego subirla.");
            modalGenerarLogoDiv.classList.add('hidden');
            paso1Div.classList.remove('hidden');
            paso2Div.classList.add('hidden');
            logoGeneradoImg.src = '';
        });
    }
});