<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
<header class="bg-white shadow-md">
    <div class="container mx-auto py-4 px-6 flex items-center justify-between">
        <a href="../../index.php" class="text-xl font-bold text-black">We-Connect</a>
        <nav class="flex items-center">
            <a href="servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
            <?php
                session_start();
                // Verificamos si el usuario está autenticado
                if(isset($_SESSION["usuario"]["usuario"])){
                    $aliasUsuario = htmlspecialchars($_SESSION['usuario']['usuario']);
                    echo '<div class="relative">';
                    echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                    echo '        <span>' . $aliasUsuario . '</span>';
                    echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                    echo '    </button>';
                    echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                    echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Panel</a>';
                    echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                    echo '        <hr class="border-gray-200">';
                    echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                    echo '    </div>';
                    echo '</div>';
                } else {
                    echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                }
                ?>
        </nav>
    </div>
</header>

<main class="container mx-auto py-12 px-6 flex-grow">
    <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Editar Servicio</h1>

        <!-- Mensajes de éxito o error -->
        <p class="text-red-500 mb-4" id="error-message" style="display: none;"></p>
        <p class="text-green-500 mb-4" id="success-message" style="display: none;"></p>

        <form action="editar_servicio.php?id=123" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="Nombre actual" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>Descripción actual</textarea>
                
                <!-- Botón para mejorar descripción -->
                <button type="button" id="mejorarDescripcion" class="mt-2 bg-yellow-500 text-black py-1 px-3 rounded-md hover:bg-yellow-600 text-sm">
                    Mejorar Descripción
                </button>
                
                <!-- Div para mostrar la sugerencia -->
                <div id="sugerenciaDescripcion" class="hidden mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-gray-700 mb-2">Sugerencia de descripción mejorada:</p>
                    <p id="textoSugerencia" class="text-gray-800 mb-3"></p>
                    <button type="button" id="aplicarSugerencia" class="bg-yellow-500 text-black py-1 px-3 rounded-md hover:bg-yellow-600 text-sm">
                        Aplicar Sugerencia
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" value="100.00" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB.</p>
                <p class="text-sm mt-2">Imagen actual: <a href="" target="_blank" class="text-blue-500 underline">Ver imagen</a></p><!-- PROGRAMAR IMAGEN -->
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Actualizar Servicio</button>
                <a href="servicio.php" class="inline-block align-baseline font-semibold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<footer class="bg-black py-4 text-center text-gray-400">
    &copy; 2025 We-Connect. Todos los derechos reservados.
</footer>
<script src="../../js/script2.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mejorarBtn = document.getElementById('mejorarDescripcion');
            const descripcionTextarea = document.getElementById('descripcion');
            const sugerenciaDiv = document.getElementById('sugerenciaDescripcion');
            const textoSugerencia = document.getElementById('textoSugerencia');
            const aplicarBtn = document.getElementById('aplicarSugerencia');

            mejorarBtn.addEventListener('click', async function() {
                const descripcionActual = descripcionTextarea.value;
                if (!descripcionActual.trim()) {
                    alert('Por favor, escribe una descripción primero.');
                    return;
                }

                mejorarBtn.disabled = true;
                mejorarBtn.textContent = 'Mejorando...';

                try {
                    const response = await fetch('../util/mejorar_descripcion.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'descripcion=' + encodeURIComponent(descripcionActual)
                    });

                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }

                    const data = await response.json();
                    textoSugerencia.textContent = data.descripcion_mejorada;
                    sugerenciaDiv.classList.remove('hidden');
                } catch (error) {
                    alert('Error al mejorar la descripción: ' + error.message);
                } finally {
                    mejorarBtn.disabled = false;
                    mejorarBtn.textContent = 'Mejorar Descripción';
                }
            });

            aplicarBtn.addEventListener('click', function() {
                descripcionTextarea.value = textoSugerencia.textContent;
                sugerenciaDiv.classList.add('hidden');
            });
        });
    </script>
</body>
</html>
