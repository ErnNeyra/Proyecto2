document.addEventListener('DOMContentLoaded', () => {
    const carrusel = document.getElementById('productos-carrusel');
    const prevButton = document.getElementById('prev-producto');
    const nextButton = document.getElementById('next-producto');
    const productoAncho = document.querySelector('#productos-carrusel > div')?.offsetWidth || 0;

    if (carrusel && prevButton && nextButton && productoAncho > 0) {
        prevButton.addEventListener('click', () => {
            carrusel.scrollLeft -= productoAncho + 16;
        });

        nextButton.addEventListener('click', () => {
            carrusel.scrollLeft += productoAncho + 16;
        });
    } else if (carrusel && prevButton && nextButton) {
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
    const mensajeRegistro = document.querySelector('.mensaje-registro');

    if (registroForm) {
        registroForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(registroForm);

            fetch('ruta/a/tu/registro.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta del servidor (Registro):', data);

                mensajeRegistro.innerHTML = '';

                if (data.success) {
                    mensajeRegistro.classList.add('success');
                    mensajeRegistro.textContent = data.message;
                    registroForm.reset();
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
                    window.location.href = '/dashboard.php';
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
