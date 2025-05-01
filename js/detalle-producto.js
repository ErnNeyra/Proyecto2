document.addEventListener('DOMContentLoaded', () => {
    // Cambio de imagen principal
    const imagenPrincipal = document.getElementById('imagen-principal');
    const miniImagenes = document.querySelectorAll('.mini-imagen');

    miniImagenes.forEach(miniatura => {
        miniatura.addEventListener('click', function() {
            const nuevaImagenSrc = this.dataset.src;
            if (imagenPrincipal) {
                imagenPrincipal.src = nuevaImagenSrc;
            }
        });
    });

    // Carrusel de productos relacionados (reutilizando lógica del carrusel principal)
    const carruselRelacionadosContainer = document.getElementById('carrusel-relacionados-container');
    const carruselRelacionados = document.getElementById('carrusel-relacionados');
    const prevRelacionadoButton = document.getElementById('prev-relacionado');
    const nextRelacionadoButton = document.getElementById('next-relacionado');

    if (carruselRelacionados && prevRelacionadoButton && nextRelacionadoButton) {
        const productoAnchoRelacionado = carruselRelacionados.children[0]?.offsetWidth + 16 || 0; // Ancho + margen
        let currentIndexRelacionados = 0;
        const numProductosRelacionados = carruselRelacionados.children.length;

        const scrollToRelacionadoIndex = (index) => {
            carruselRelacionados.style.transform = `translateX(-${index * productoAnchoRelacionado}px)`;
        };

        const scrollToNextRelacionado = () => {
            currentIndexRelacionados = Math.min(currentIndexRelacionados + 1, numProductosRelacionados - 1);
            scrollToRelacionadoIndex(currentIndexRelacionados);
        };

        const scrollToPrevRelacionado = () => {
            currentIndexRelacionados = Math.max(currentIndexRelacionados - 1, 0);
            scrollToRelacionadoIndex(currentIndexRelacionados);
        };

        prevRelacionadoButton.addEventListener('click', scrollToPrevRelacionado);
        nextRelacionadoButton.addEventListener('click', scrollToNextRelacionado);

        // Ajuste de padding para el "peek" (opcional)
        if (carruselRelacionadosContainer) {
            carruselRelacionadosContainer.style.paddingLeft = `${(carruselRelacionadosContainer.offsetWidth - productoAnchoRelacionado) / 2}px`;
            carruselRelacionadosContainer.style.paddingRight = `${(carruselRelacionadosContainer.offsetWidth - productoAnchoRelacionado) / 2}px`;
            carruselRelacionados.style.marginLeft = `-${(carruselRelacionadosContainer.offsetWidth - productoAnchoRelacionado) / 2}px`;
            carruselRelacionados.style.marginRight = `-${(carruselRelacionadosContainer.offsetWidth - productoAnchoRelacionado) / 2}px`;
        }
    }
});