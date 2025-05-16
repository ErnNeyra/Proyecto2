document.addEventListener('DOMContentLoaded', () => {

    // --- Lightbox de Imágenes (Mantienes tu implementación) ---
    const mainImageContainer = document.getElementById('main-image-container');
    const mainImage = document.getElementById('main-image');
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const closeLightboxButton = document.getElementById('close-lightbox');
    const prevImageButton = document.getElementById('prev-image');
    const nextImageButton = document.getElementById('next-image');
    const lightboxCounter = document.getElementById('lightbox-counter');
    const miniImages = document.querySelectorAll('.mini-image');

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
        document.addEventListener('keydown', function (event) {
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
                //  element.classList.remove('reveal');
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
        registroForm.addEventListener('submit', function (event) {
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
    const mensajeLogin = document.querySelector('.mensaje-login'); // Asegúrate deque exista
    if (loginForm && mensajeLogin) {
        loginForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(loginForm);
            // Asegúrate de que la ruta a tu script PHP sea correcta
            fetch('ruta/a/tu/login.php', { // O la ruta correcta para el login
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
                        // Redirige al usuario después del login exitoso
                        window.location.href = data.redirect || '/panelUsuario.php'; // Usa la ruta de redirección del servidor o una por defecto
                    } else if (data.message) {
                        mensajeLogin.classList.add('form__error'); // Usa tu clase de error CSS
                        mensajeLogin.textContent = data.message;
                    } else {
                        mensajeLogin.classList.add('form__error');
                        mensajeLogin.textContent = 'Ocurrió un error al iniciar sesión.';
                    }
                })
                .catch(error => {
                    console.error('Error al enviar la solicitud de inicio de sesión:', error);
                    mensajeLogin.classList.add('form__error');
                    mensajeLogin.textContent = 'Error al comunicarse con el servidor.';
                });
        });
    } else {
        console.warn('Formulario de inicio de sesión o mensaje no encontrados.');
    }

    // --- User Dropdown (Mantienes tu implementación) ---
    const userDropdownButton = document.getElementById('user-dropdown-button'); // Asegúrate de que exista
    const userDropdown = document.getElementById('user-dropdown'); // Asegúrate de que exista

    if (userDropdownButton && userDropdown) {
        userDropdownButton.addEventListener('click', function (event) {
            event.stopPropagation(); // Evita que el evento se propague al documento
            userDropdown.classList.toggle('hidden');
            // Actualiza el atributo aria-expanded para accesibilidad
            userDropdownButton.setAttribute('aria-expanded', !userDropdown.classList.contains('hidden'));
        });

        // Cierra el dropdown si se hace clic fuera de él
        document.addEventListener('click', function (event) {
            if (!userDropdown.classList.contains('hidden') && !userDropdownButton.parentElement.contains(event.target)) {
                userDropdown.classList.add('hidden');
                userDropdownButton.setAttribute('aria-expanded', 'false');
            }
        });

        // Cierra el dropdown al cambiar el tamaño de la ventana (útil para responsive)
        window.addEventListener('resize', function () {
            if (!userDropdown.classList.contains('hidden')) {
                userDropdown.classList.add('hidden');
                userDropdownButton.setAttribute('aria-expanded', 'false');
            }
        });

    } else {
        console.warn('Botón o menú desplegable de usuario no encontrados.');
    }

    // --- Header Sticky & Shadow (Mantienes tu implementación) ---
    const header = document.querySelector('header'); // Asegúrate de que tu header esté etiquetado así

    if (header) {
        const toggleHeaderShadow = () => {
            // Si el usuario ha hecho scroll más de 50px, añade la clase 'scrolled' al header
            if (window.scrollY > 50) {
                header.classList.add('scrolled'); // Añade la sombra y el fondo
            } else {
                header.classList.remove('scrolled'); // Quita la sombra y el fondo
            }
        };

        // Ejecutar al cargar la página y al hacer scroll
        window.addEventListener('scroll', toggleHeaderShadow);
        window.addEventListener('load', toggleHeaderShadow);

    } else {
        console.warn('Elemento header no encontrado.');
    }

    // --- Mobile Menu (Mantienes tu implementación) ---
    const mobileMenuButton = document.getElementById('mobile-menu-button'); // El botón que abre el menú
    const mobileNav = document.getElementById('mobile-nav'); // La navegación móvil

    if (mobileMenuButton && mobileNav) {
        const toggleMobileMenu = () => {
            mobileNav.classList.toggle('hidden'); // Muestra u oculta la navegación
            document.body.classList.toggle('nav-mobile-open'); // Evita el scroll del body mientras el menú está abierto

            // Cambia el icono del botón (hamburguesa o cruz)
            const icon = mobileMenuButton.querySelector('svg');
            if (mobileNav.classList.contains('hidden')) {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>'; // Icono de hamburguesa
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'; // Icono de cruz
            }
        };

        mobileMenuButton.addEventListener('click', toggleMobileMenu);

        // Cierra el menú al hacer clic en un enlace (opcional)
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.classList.add('hidden');
                document.body.classList.remove('nav-mobile-open');
                mobileMenuButton.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>';
            });
        });

    } else {
        console.warn('Botón o navegación móvil no encontrados.');
    }

    // --- Form Validation (Mantienes tu implementación) ---
    const form = document.querySelector('form'); // Selecciona el primer formulario (ajusta si tienes varios)
    const precioInput = document.getElementById('precio'); // Asegúrate de que exista
    const nombreInput = document.getElementById('nombre'); // Asegúrate de que exista
    const descripcionInput = document.getElementById('descripcion'); // Asegúrate de que exista

    if (form && precioInput && nombreInput && descripcionInput) {
        const errorContainer = document.createElement('div');
        errorContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 hidden';
        errorContainer.setAttribute('role', 'alert');

        // Inserta el contenedor de errores antes del formulario
        form.parentNode.insertBefore(errorContainer, form);

        form.addEventListener('submit', function (event) {
            let errors = [];

            if (nombreInput.value.length < 3 || nombreInput.value.length > 100) {
                errors.push('El nombre debe tener entre 3 y 100 caracteres.');
            }

            if (descripcionInput.value.length < 10 || descripcionInput.value.length > 500) {
                errors.push('La descripción debe tener entre 10 y 500 caracteres.');
            }

            if (isNaN(precioInput.value) || parseFloat(precioInput.value) <= 0) {
                errors.push('El precio debe ser un número mayor que 0.');
                
}
  if (errors.length > 0) {
            event.preventDefault(); // Detiene el envío del formulario
            errorContainer.innerHTML = errors.map(error => `<p>${error}</p>`).join('');
            errorContainer.classList.remove('hidden');
            // Hace scroll hasta el primer error
            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            errorContainer.classList.add('hidden'); // Oculta el contenedor si no hay errores
        }
    });
} else {
    console.warn('Formulario o campos de validación no encontrados.');
}

// --- Description Improvement & Logo Generation (Mantienes tu implementación) ---
const mejorarDescripcionBtn = document.getElementById('mejorarDescripcion'); // Asegúrate de que exista
const sugerenciaDescripcionDiv = document.getElementById('sugerenciaDescripcion'); // Asegúrate de que exista
const textoSugerenciaParrafo = document.getElementById('textoSugerencia'); // Asegúrate de que exista
const aplicarSugerenciaBtn = document.getElementById('aplicarSugerencia'); // Asegúrate de que exista
const descripcionTextarea = document.getElementById('descripcion'); // Asegúrate de que exista

const generarLogoBtn = document.getElementById('generarLogo'); // Asegúrate de que exista
const modalGenerarLogoDiv = document.getElementById('modalGenerarLogo'); // Asegúrate de que exista
const cerrarModalBtn = document.getElementById('cerrarModal'); // Asegúrate de que exista
const generarLogoModalBtn = document.getElementById('generarLogoBtn'); // Asegúrate de que exista
const logoDescripcionTextarea = document.getElementById('logoDescripcion'); // Asegúrate de que exista
const paso1Div = document.getElementById('paso1'); // Asegúrate de que exista
const paso2Div = document.getElementById('paso2'); // Asegúrate de que exista
const logoGeneradoImg = document.getElementById('logoGenerado'); // Asegúrate de que exista
const loadingIndicatorDiv = document.getElementById('loadingIndicator'); // Asegúrate de que exista
const guardarLogoBtn = document.getElementById('guardarLogo'); // Asegúrate de que exista
const imagenInputFile = document.getElementById('imagen'); // Asegúrate de que exista

if (mejorarDescripcionBtn) {
    mejorarDescripcionBtn.addEventListener('click', function () {
        const descripcion = descripcionTextarea.value.trim();
        if (descripcion !== '') {
            mejorarDescripcionBtn.disabled = true;
            mejorarDescripcionBtn.textContent = 'Cargando...';
            fetch('util/mejorar_descripcion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    descripcion: descripcion
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        textoSugerenciaParrafo.textContent = data.success;
                        sugerenciaDescripcionDiv.classList.remove('hidden');
                    } else if (data.error) {
                        alert('Error: ' + data.error);
                    } else {
                        alert('Error desconocido al mejorar la descripción.');
                    }
                })
                .catch(error => {
                    console.error('Error al mejorar la descripción:', error);
                    alert('Error al comunicarse con el servidor.');
                })
                .finally(() => {
                    mejorarDescripcionBtn.disabled = false;
                    mejorarDescripcionBtn.textContent = 'Mejorar Descripción';
                });
        } else {
            alert('Por favor, ingresa una descripción para mejorar.');
        }
    });
}

if (aplicarSugerenciaBtn) {
    aplicarSugerenciaBtn.addEventListener('click', function () {
        descripcionTextarea.value = textoSugerenciaParrafo.textContent;
        sugerenciaDescripcionDiv.classList.add('hidden');
    });
}

if (generarLogoBtn) {
    generarLogoBtn.addEventListener('click', function () {
        modalGenerarLogoDiv.classList.remove('hidden');
        paso1Div.classList.remove('hidden');
        paso2Div.classList.add('hidden');
        logoDescripcionTextarea.value = '';
    });
}

if (cerrarModalBtn) {
    cerrarModalBtn.addEventListener('click', function () {
        modalGenerarLogoDiv.classList.add('hidden');
        paso1Div.classList.remove('hidden');
        paso2Div.classList.add('hidden');
        logoGeneradoImg.src = '';
    });
}

if (generarLogoModalBtn) {
    generarLogoModalBtn.addEventListener('click', function () {
        const descripcionLogo = logoDescripcionTextarea.value.trim();
        if (descripcionLogo !== '') {
            loadingIndicatorDiv.classList.remove('hidden');
            paso1Div.classList.add('hidden');
            // Simulación de una llamada a una API de generación de logos
            setTimeout(function () {
                const imageUrl = '[https://via.placeholder.com/150/4682B4/FFFFFF?Text=](https://via.placeholder.com/150/4682B4/FFFFFF?Text=)' + encodeURIComponent('Logo para ' + descripcionLogo);
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
    guardarLogoBtn.addEventListener('click', function () {
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