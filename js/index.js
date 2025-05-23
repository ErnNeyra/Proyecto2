// js/index.js

document.addEventListener('DOMContentLoaded', () => {

    /* ==============================================
       1. Efecto de Parallax en la Hero Section
       ============================================== */
    const stars = document.querySelector('.stars');
    const twinkling = document.querySelector('.twinkling');

    if (stars && twinkling) {
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;

            // Ajusta estos factores para controlar la velocidad del parallax
            // Cuanto menor el factor, más lento se mueve el fondo
            const starsSpeed = 0.05; // Más lento
            const twinklingSpeed = 0.1; // Un poco más rápido

            stars.style.transform = `translateY(${scrollY * starsSpeed}px)`;
            twinkling.style.transform = `translateY(${scrollY * twinklingSpeed}px)`;
            // También se podría usar background-position-y para efectos más complejos
            // stars.style.backgroundPositionY = `${scrollY * starsSpeed}px`;
        });
    }

    /* ==============================================
       2. Header Sticky con Efecto de Sombra y Opacidad
       ============================================== */
    const header = document.querySelector('header');

    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) { // Añade la clase 'scrolled' después de 50px de scroll
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    /* ==============================================
       3. Desplegable del Menú de Usuario
       ============================================== */
    const userDropdownButton = document.getElementById('user-dropdown-button');
    const userDropdown = document.getElementById('user-dropdown');

    if (userDropdownButton && userDropdown) {
        userDropdownButton.addEventListener('click', (event) => {
            event.stopPropagation(); // Evita que el clic se propague al documento
            userDropdown.classList.toggle('show');
            // Asegura que el dropdown esté posicionado correctamente
            const buttonRect = userDropdownButton.getBoundingClientRect();
            // userDropdown.style.top = `${buttonRect.bottom + 8}px`; // No necesario si ya está en CSS
            // userDropdown.style.right = `${window.innerWidth - buttonRect.right}px`; // No necesario si ya está en CSS
        });

        // Cierra el dropdown si se hace clic fuera de él
        document.addEventListener('click', (event) => {
            if (!userDropdown.contains(event.target) && !userDropdownButton.contains(event.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }

    /* ==============================================
       4. Animaciones al Hacer Scroll (Fade In Up)
       ============================================== */
    const fadeInUpElements = document.querySelectorAll('.fade-in-up-scroll');

    const observerOptions = {
        root: null, // viewport como root
        rootMargin: '0px',
        threshold: 0.1 // 10% del elemento visible
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target); // Dejar de observar una vez que se ha animado
            }
        });
    }, observerOptions);

    fadeInUpElements.forEach(element => {
        observer.observe(element);
    });

    /* ==============================================
       5. Carrusel de Productos Arrastrable
       ============================================== */
    const productosCarruselContainer = document.getElementById('productos-carrusel-container');
    const productosCarrusel = document.getElementById('productos-carrusel');
    const prevProductButton = document.getElementById('prev-product');
    const nextProductButton = document.getElementById('next-product');

    let isDragging = false;
    let startX;
    let scrollLeft;

    if (productosCarruselContainer && productosCarrusel) {
        productosCarruselContainer.addEventListener('mousedown', (e) => {
            isDragging = true;
            productosCarruselContainer.classList.add('dragging');
            startX = e.pageX - productosCarruselContainer.offsetLeft;
            scrollLeft = productosCarrusel.scrollLeft;
        });

        productosCarruselContainer.addEventListener('mouseleave', () => {
            isDragging = false;
            productosCarruselContainer.classList.remove('dragging');
        });

        productosCarruselContainer.addEventListener('mouseup', () => {
            isDragging = false;
            productosCarruselContainer.classList.remove('dragging');
        });

        productosCarruselContainer.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - productosCarruselContainer.offsetLeft;
            const walk = (x - startX) * 2; // Multiplica para un scroll más rápido
            productosCarrusel.scrollLeft = scrollLeft - walk;
        });

        // Desplazamiento con botones
        if (prevProductButton) {
            prevProductButton.addEventListener('click', () => {
                // Calcula el ancho de una tarjeta + el gap
                const cardWidth = productosCarrusel.querySelector('.producto-card').offsetWidth;
                const gap = parseInt(getComputedStyle(productosCarrusel).gap);
                productosCarrusel.scrollBy({
                    left: -(cardWidth + gap),
                    behavior: 'smooth'
                });
            });
        }

        if (nextProductButton) {
            nextProductButton.addEventListener('click', () => {
                const cardWidth = productosCarrusel.querySelector('.producto-card').offsetWidth;
                const gap = parseInt(getComputedStyle(productosCarrusel).gap);
                productosCarrusel.scrollBy({
                    left: (cardWidth + gap),
                    behavior: 'smooth'
                });
            });
        }
    }

   
});