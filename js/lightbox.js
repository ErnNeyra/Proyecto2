document.addEventListener('DOMContentLoaded', () => {
    const contenedorImagenPrincipal = document.getElementById('contenedor-imagen-principal');
    const imagenPrincipal = document.getElementById('imagen-principal');
    const lightbox = document.getElementById('lightbox');
    const lightboxImagen = document.getElementById('lightbox-imagen');
    const cerrarLightboxButton = document.getElementById('cerrar-lightbox');
    const miniImagenes = document.querySelectorAll('.mini-imagen');
    const anteriorImagenButton = document.getElementById('anterior-imagen');
    const siguienteImagenButton = document.getElementById('siguiente-imagen');

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
        }
    }

    if (contenedorImagenPrincipal && lightbox && lightboxImagen && cerrarLightboxButton && anteriorImagenButton && siguienteImagenButton) {
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

        // Actualizar la imagen principal y, si el lightbox está abierto, su imagen también
        miniImagenes.forEach(miniatura => {
            miniatura.addEventListener('click', function() {
                const nuevaImagenSrc = this.dataset.src;
                imagenPrincipal.src = nuevaImagenSrc;
                const nuevoIndice = listaImagenesLightbox.indexOf(nuevaImagenSrc);
                if (!lightbox.classList.contains('hidden')) {
                    indiceImagenActual = nuevoIndice;
                    mostrarImagenLightbox(indiceImagenActual);
                }
            });
        });
    }
});