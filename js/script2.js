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

    if (mainImageContainer && lightbox && lightboxImage && closeLightboxButton &&
        prevImageButton && nextImageButton && lightboxCounter && miniImages.length > 0) {

        let lightboxImages = Array.from(miniImages).map(mini => mini.dataset.src);
        if (!lightboxImages.includes(mainImage.src)) {
            lightboxImages.unshift(mainImage.src);
        }

        const showLightboxImage = (index) => {
            if (index >= 0 && index < lightboxImages.length) {
                lightboxImage.src = lightboxImages[index];
                updateLightboxCounter(index + 1, lightboxImages.length);
                updateMiniImageActiveState(lightboxImages[index], miniImages);
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

        mainImageContainer.addEventListener('click', () => {
            currentImageIndex = lightboxImages.indexOf(mainImage.src);
            showLightboxImage(currentImageIndex);
            lightbox.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
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

        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                lightbox.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (!lightbox.classList.contains('hidden')) {
                if (event.key === 'ArrowLeft') {
                    prevImageButton.click();
                } else if (event.key === 'ArrowRight') {
                    nextImageButton.click();
                } else if (event.key === 'Escape') {
                    closeLightboxButton.click();
                }
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

        updateMiniImageActiveState(mainImage.src, miniImages);

    } else {
        console.warn('Elementos del lightbox no encontrados o no hay miniaturas.');
    }

    const elementsToReveal = document.querySelectorAll('[data-reveal], .js-fade-in-up');

    const revealElementsOnScroll = () => {
        elementsToReveal.forEach(element => {
            const topElement = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            const pointOfReveal = element.dataset.revealOffset || 150;
            if (topElement < windowHeight - pointOfReveal && topElement > -element.offsetHeight) {
                element.classList.add('reveal');
            }
        });
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal');
                }
            });
        }, {
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.1
        });

        elementsToReveal.forEach(element => {
            observer.observe(element);
        });

        revealElementsOnScroll();

    } else {
        window.addEventListener('scroll', revealElementsOnScroll);
        window.addEventListener('load', revealElementsOnScroll);
        revealElementsOnScroll();
    }

    const registroForm = document.getElementById('registro-form');
    const mensajeRegistro = document.querySelector('.mensaje-registro');

    if (registroForm && mensajeRegistro) {
        registroForm.addEventListener('submit', function (event) {
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
                    mensajeRegistro.classList.remove('success', 'form__error');
                    if (data.success) {
                        mensajeRegistro.classList.add('success');
                        mensajeRegistro.textContent = data.message;
                        registroForm.reset();
                    } else if (data.message) {
                        mensajeRegistro.classList.add('form__error');
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

    const loginForm = document.getElementById('login-form');
    const mensajeLogin = document.querySelector('.mensaje-login');
    if (loginForm && mensajeLogin) {
        loginForm.addEventListener('submit', function (event) {
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
                    mensajeLogin.classList.remove('success', 'form__error');
                    if (data.success) {
                        mensajeLogin.classList.add('success');
                        mensajeLogin.textContent = data.message;
                        window.location.href = data.redirect || '/panelUsuario.php';
                    } else if (data.message) {
                        mensajeLogin.classList.add('form__error');
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

    const userDropdownButton = document.getElementById('user-dropdown-button');
    const userDropdown = document.getElementById('user-dropdown');

    if (userDropdownButton && userDropdown) {
        userDropdownButton.addEventListener('click', function (event) {
            event.stopPropagation();
            userDropdown.classList.toggle('hidden');
            userDropdownButton.setAttribute('aria-expanded', !userDropdown.classList.contains('hidden'));
        });

        document.addEventListener('click', function (event) {
            if (!userDropdown.classList.contains('hidden') && !userDropdownButton.parentElement.contains(event.target)) {
                userDropdown.classList.add('hidden');
                userDropdownButton.setAttribute('aria-expanded', 'false');
            }
        });

        window.addEventListener('resize', function () {
            if (!userDropdown.classList.contains('hidden')) {
                userDropdown.classList.add('hidden');
                userDropdownButton.setAttribute('aria-expanded', 'false');
            }
        });

    } else {
        console.warn('Botón o menú desplegable de usuario no encontrados.');
    }

    const header = document.querySelector('header');

    if (header) {
        const toggleHeaderShadow = () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        };

        window.addEventListener('scroll', toggleHeaderShadow);
        window.addEventListener('load', toggleHeaderShadow);

    } else {
        console.warn('Elemento header no encontrado.');
    }

    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileNav = document.getElementById('mobile-nav');

    if (mobileMenuButton && mobileNav) {
        const toggleMobileMenu = () => {
            mobileNav.classList.toggle('hidden');
            document.body.classList.toggle('nav-mobile-open');
            const icon = mobileMenuButton.querySelector('svg');
            if (mobileNav.classList.contains('hidden')) {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>';
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
            }
        };

        mobileMenuButton.addEventListener('click', toggleMobileMenu);

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

    const form = document.querySelector('form');
    const precioInput = document.getElementById('precio');
    const nombreInput = document.getElementById('nombre');
    const descripcionInput = document.getElementById('descripcion');

    if (form && precioInput && nombreInput && descripcionInput) {
        const errorContainer = document.createElement('div');
        errorContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 hidden';
        errorContainer.setAttribute('role', 'alert');

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
                event.preventDefault();
                errorContainer.innerHTML = errors.map(error => `<p>${error}</p>`).join('');
                errorContainer.classList.remove('hidden');
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                errorContainer.classList.add('hidden');
            }
        });
    } else {
        console.warn('Formulario o campos de validación no encontrados.');
    }

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
                    .then(async response => {
                        if (!response.ok) {
                            const errorText = await response.text();
                            throw new Error('HTTP ' + response.status + ': ' + errorText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            textoSugerenciaParrafo.textContent = data.success;
                            sugerenciaDescripcionDiv.classList.remove('hidden');
                        } else if (data.error) {
                            console.error('Error: ' + data.error);
                        } else {
                            console.error('Error desconocido al mejorar la descripción.');
                        }
                    })
                    .catch(error => {
                        console.error('Error al mejorar la descripción:', error);
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
            alert("La URL del logo generado es: " + logoUrl + ". Puedes hacer clic derecho y 'Guardar imagen como...' para descargarla y luego subirla.");
            modalGenerarLogoDiv.classList.add('hidden');
            paso1Div.classList.remove('hidden');
            paso2Div.classList.add('hidden');
            logoGeneradoImg.src = '';
        });
    }
});