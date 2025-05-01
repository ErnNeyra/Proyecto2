document.addEventListener('DOMContentLoaded', () => {
    const carrusel = document.getElementById('productos-carrusel');
    const prevButton = document.getElementById('prev-producto');
    const nextButton = document.getElementById('next-producto');
    const productoAncho = document.querySelector('#productos-carrusel > div')?.offsetWidth || 0; // Obtener el ancho de un producto

    if (carrusel && prevButton && nextButton && productoAncho > 0) {
        prevButton.addEventListener('click', () => {
            carrusel.scrollLeft -= productoAncho + 16; // Ancho del producto + margen derecho (4rem = 16px)
        });

        nextButton.addEventListener('click', () => {
            carrusel.scrollLeft += productoAncho + 16; // Ancho del producto + margen derecho (4rem = 16px)
        });
    } else if (carrusel && prevButton && nextButton) {
        // Fallback si no se puede obtener el ancho del producto
        prevButton.addEventListener('click', () => {
            carrusel.scrollLeft -= carrusel.offsetWidth;
        });

        nextButton.addEventListener('click', () => {
            carrusel.scrollLeft += carrusel.offsetWidth;
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const registroForm = document.getElementById('registro-form');
    const mensajeRegistro = document.querySelector('.mensaje-registro'); // Elemento para mostrar mensajes

    if (registroForm) {
        registroForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Evita la recarga de la página

            const formData = new FormData(registroForm);

            fetch('ruta/a/tu/registro.php', { // Reemplaza 'ruta/a/tu/registro.php' con la URL correcta de tu script PHP
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor (Registro):', data);

                // Limpiar mensajes previos
                mensajeRegistro.innerHTML = '';

                if (data.success) {
                    mensajeRegistro.classList.add('success');
                    mensajeRegistro.textContent = data.message;
                    registroForm.reset(); // Limpiar el formulario en caso de éxito
                } else if (data.errors) {
                    mensajeRegistro.classList.remove('success');
                    let erroresHtml = '<ul class="errores">';
                    for (const key in data.errors) {
                        erroresHtml += `<li>${data.errors[key]}</li>`;
                    }
                    erroresHtml += '</ul>';
                    mensajeRegistro.innerHTML = erroresHtml;
                } else if (data.message) {
                    mensajeRegistro.classList.remove('success');
                    mensajeRegistro.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error al enviar la solicitud de registro:', error);
                mensajeRegistro.classList.remove('success');
                mensajeRegistro.textContent = 'Error al comunicarse con el servidor.';
            });
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const mensajeLogin = document.querySelector('.mensaje-login'); // Elemento para mostrar mensajes de inicio de sesión

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(loginForm);

            fetch('ruta/a/tu/login.php', { // Reemplaza 'ruta/a/tu/login.php' con la URL correcta
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor (Login):', data);

                // Limpiar mensajes previos
                mensajeLogin.innerHTML = '';
                mensajeLogin.classList.remove('success'); // Remover clase de éxito si estaba presente

                if (data.success) {
                    mensajeLogin.classList.add('success');
                    mensajeLogin.textContent = data.message;
                    // Redirigir al usuario a la página principal o a su panel de control
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
