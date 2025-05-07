<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Conecta con otros emprendedores</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style2.css"> </head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="php/listado.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="php/contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <a href="php/registro.php" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="php/login.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <section class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-6">Conecta y crece con otros emprendedores</h1>
            <p class="text-lg text-gray-600 mb-8">La plataforma ideal para que nuevos emprendedores colaboren, encuentren soluciones y hagan crecer sus negocios.</p>
            <a href="php/registro.php" class="bg-yellow-500 text-black py-3 px-6 rounded-md hover:bg-yellow-600 text-lg">¡Únete ahora!</a>
        </section>

        <section class="py-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Algunos de nuestros emprendedores ofrecen...</h2>
            <div class="relative">
                <div id="productos-carrusel-container" class="overflow-hidden relative">
                    <div id="productos-carrusel" class="whitespace-nowrap scroll-smooth transition-transform duration-300 py-4 -ml-4 pl-4">
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/cccccc/eeeeee?Text=Producto%201" alt="Producto 1" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Producto/Servicio 1</h3>
                                <p class="text-gray-600 text-sm">Breve descripción del producto o servicio.</p>
                            </div>
                        </div>
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/aaaaaa/eeeeee?Text=Servicio%202" alt="Servicio 2" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Servicio 2</h3>
                                <p class="text-gray-600 text-sm">Descripción concisa de este servicio.</p>
                            </div>
                        </div>
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/bbbbbb/eeeeee?Text=Producto%203" alt="Producto 3" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Producto 3</h3>
                                <p class="text-gray-600 text-sm">Un vistazo a las características de este producto.</p>
                            </div>
                        </div>
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/dddddd/eeeeee?Text=Producto%204" alt="Producto 4" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Producto/Servicio 4</h3>
                                <p class="text-gray-600 text-sm">Otra oferta interesante.</p>
                            </div>
                        </div>
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/eeeeee/eeeeee?Text=Servicio%205" alt="Servicio 5" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Servicio 5</h3>
                                <p class="text-gray-600 text-sm">Un servicio más para explorar.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="prev-producto" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-black rounded-full w-10 h-10 flex items-center justify-center -ml-2 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="next-producto" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-black rounded-full w-10 h-10 flex items-center justify-center -mr-2 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <div id="carousel-indicators" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                </div>
                <button id="pause-play-button" class="absolute bottom-10 right-4 bg-gray-300 hover:bg-gray-400 text-black rounded-full w-8 h-8 flex items-center justify-center focus:outline-none">
                    <svg id="play-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A3 3 0 0010 9.87v4.263a3 3 0 001.555 2.606l3.197-2.132a3 3 0 000-4.264z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <svg id="pause-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6"></path></svg>
                </button>
            </div>
        </section>

        <section class="py-12 bg-gray-200 rounded-md">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Lo que dicen otros emprendedores...</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-6">
                <div class="bg-white rounded-md shadow-md p-6 text-center border border-gray-200">
                    <svg class="mx-auto h-8 w-8 text-yellow-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-gray-700 mb-4">"We-Connect ha sido fundamental para encontrar colaboradores en áreas que no domino. ¡Altamente recomendado!"</p>
                    <p class="font-semibold text-black">- Carlos Pérez, Fundador de Tech Solutions</p>
                </div>
                <div class="bg-white rounded-md shadow-md p-6 text-center border border-gray-200">
                    <svg class="mx-auto h-8 w-8 text-yellow-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-gray-700 mb-4">"La posibilidad de ver los servicios de otros emprendedores me ha dado nuevas ideas para mi propio negocio."</p>
                    <p class="font-semibold text-black">- Ana Gómez, Emprendedora Textil</p>
                </div>
                <div class="bg-white rounded-md shadow-md p-6 text-center border border-gray-200">
                    <svg class="mx-auto h-8 w-8 text-yellow-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-gray-700 mb-4">"Una plataforma muy útil para conectar con gente que entiende los desafíos de emprender."</p>
                    <p class="font-semibold text-black">- Javier López, Consultor de Marketing</p>
                </div>
            </div>
        </section>

        <section class="py-12">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">¿Cómo funciona?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354l-2.894 5.788a2 2 0 001.167 2.588l6.723 1.94a2 2 0 001.167-2.588L12 4.354z"></path></svg>
                    <h3 class="font-semibold text-black mt-2">Regístrate</h3>
                    <p class="text-gray-600">Crea tu perfil y cuéntale a la comunidad sobre tu emprendimiento.</p>
                </div>
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m0 0l2 2m-2-2h2a2 2 0 012 2v1h1m-1-1H9a2 2 0 00-2 2v1h1m-1-1l2-2m-2-2l7-7 7 7m-2-2h2a2 2 0 012 2v1h1m-1-1H9a2 2 0 00-2 2v1h1m-1-1l2-2m-2-2l7-7 7 7"></path></svg>
                    <h3 class="font-semibold text-black mt-2">Explora</h3>
                    <p class="text-gray-600">Descubre perfiles de otros emprendedores y sus ofertas de productos o servicios.</p>
                </div>
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                    <h3 class="font-semibold text-black mt-2">Conecta</h3>
                    <p class="text-gray-600">Accede a herramientas premium para contactar y colaborar directamente (próximamente).</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        <p>&copy; 2025 We-Connect. Todos los derechos reservados.</p>
    </footer>

    <script src="../js/script2.js"></script>
</body>
</html>