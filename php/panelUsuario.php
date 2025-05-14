<?php
    session_start();
   
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Panel de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style2.css"> <style>
        /* Estilos adicionales para el panel de usuario */
        .user-panel {
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
        }

        .user-panel h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #374151;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .account-info h3, .actions h3 {
            font-size: 1.125rem;
            font-weight: medium;
            color: #4b5563;
            margin-bottom: 0.75rem;
        }

        .account-info p {
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .actions ul {
            list-style: none;
            padding: 0;
        }

        .actions li {
            margin-bottom: 0.75rem;
        }

        .actions li a {
            color: #facc15; /* Tailwind yellow-500 */
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }

        .actions li a:hover {
            color: #eab308; /* Tailwind yellow-600 */
            text-decoration: underline;
        }

        .logout-button {
            background-color: #ef4444; /* Tailwind red-500 */
            color: white;
            font-weight: bold;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            width: 100%; /* O ajusta según necesites */
            display: block;
            text-align: center;
        }

        .logout-button:hover {
            background-color: #dc2626; /* Tailwind red-600 */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="listado.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-8">
        <div class="user-panel">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Panel de Usuario</h2>
            <div class="mb-4 account-info">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Información de la Cuenta</h3>
                <p class="text-gray-600">Nombre: <span id="user-name"><?php $_SESSION['usuario']; ?></span></p>
                <p class="text-gray-600">Email: <span id="user-email">email@ejemplo.com</span></p>
            </div>
            <div class="mb-4 actions">
                <h3 class="text-lg font-medium text-gray-700 mb-2">Acciones</h3>
                <ul class="space-y-2">
                    <li> <a href="editarPerfil.php"
     class="block w-full text-center bg-blue-600 text-white text-lg font-bold py-3 px-6 rounded-lg shadow hover:bg-blue-700 transition-all duration-200">
     Editar Perfil
  </a></li>
                    <li><button id="logout-button" class="logout-button">Cerrar Sesión</button></li>
                </ul>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simulación de datos del usuario (en una aplicación real vendrían del backend)
            const userNameSpan = document.getElementById('user-name');
            const userEmailSpan = document.getElementById('user-email');
            const logoutButton = document.getElementById('logout-button');

            // Reemplaza estos valores con la información real del usuario obtenida del backend
            if (userNameSpan) {
                userNameSpan.textContent = 'Usuario Conectado';
            }
            if (userEmailSpan) {
                userEmailSpan.textContent = 'usuario@weconnect.com';
            }

            if (logoutButton) {
                logoutButton.addEventListener('click', function() {
                    // Aquí iría la lógica para cerrar la sesión del usuario
                    // Esto generalmente implica una petición al backend.
                    alert('Cerrando sesión...');
                    window.location.href = '/'; // Redirigir a la página principal después de cerrar sesión
                });
            }
        });
    </script>
</body>
</html>
