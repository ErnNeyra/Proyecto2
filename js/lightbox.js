document.addEventListener('DOMContentLoaded', () => {
    // Elementos del lightbox y la imagen principal
    const contenedorImagenPrincipal = document.getElementById('contenedor-imagen-principal');
    const imagenPrincipal = document.getElementById('imagen-principal');
    const lightbox = document.getElementById('lightbox');
    const lightboxImagen = document.getElementById('lightbox-imagen');
    const cerrarLightboxButton = document.getElementById('cerrar-lightbox');
    // Los botones anterior/siguiente ya no son necesarios para una sola imagen
    const anteriorImagenButton = document.getElementById('anterior-imagen');
    const siguienteImagenButton = document.getElementById('siguiente-imagen');


    // Verificar que los elementos esenciales existan
    if (!contenedorImagenPrincipal || !imagenPrincipal || !lightbox || !lightboxImagen || !cerrarLightboxButton) {
        // Si no se encuentran los elementos, es posible que el lightbox no esté en esta página o falten IDs.
        // console.warn('Elementos necesarios para el lightbox no encontrados.');
        return; // Salir del script si los elementos no están presentes
    }

    // Opcional: Ocultar los botones anterior/siguiente si existen, ya que solo hay una imagen
     if (anteriorImagenButton) anteriorImagenButton.style.display = 'none';
     if (siguienteImagenButton) siguienteImagenButton.style.display = 'none';


    // Abrir el lightbox al hacer clic en el contenedor de la imagen principal
    contenedorImagenPrincipal.addEventListener('click', () => {
        lightboxImagen.src = imagenPrincipal.src; // Establecer la fuente de la imagen del lightbox
        lightbox.classList.remove('hidden'); // Mostrar el lightbox (asumo que 'hidden' lo oculta)
    });

    // Cerrar el lightbox al hacer clic en el botón de cerrar
    cerrarLightboxButton.addEventListener('click', () => {
        lightbox.classList.add('hidden'); // Ocultar el lightbox
    });

    // Cerrar el lightbox al hacer clic fuera de la imagen (directamente en el overlay)
    lightbox.addEventListener('click', (event) => {
        // Si el clic fue en el fondo del lightbox (no en la imagen ni los botones)
        if (event.target === lightbox) {
            lightbox.classList.add('hidden'); // Ocultar el lightbox
        }
    });

    // Ya no hay lógica para miniaturas ni navegación entre imágenes.
});