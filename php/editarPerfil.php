<?php
    session_start();
    require('config.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style2.css">
    <style>
        /* Estilos adicionales para el formulario de editar perfil */
        .edit-profile-panel {
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
        }

        .edit-profile-panel h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #374151;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: medium;
            color: #4b5563;
            margin-bottom: 0.5rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-sizing: border-box;
        }

        .form-group .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-actions button {
            background-color: #facc15; /* Tailwind yellow-500 */
            color: black;
            font-weight: bold;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
            width: 100%;
        }

        .form-actions button:hover {
            background-color: #eab308; /* Tailwind yellow-600 */
        }

        .success-message {
            color: #16a34a; /* Tailwind green-500 */
            margin-bottom: 1rem;
            font-weight: medium;
        }

        .error-message-overall {
            color: #ef4444; /* Tailwind red-500 */
            margin-bottom: 1rem;
            font-weight: medium;
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
                <a href="registro.php" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="login.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
                <a href="panelUsuario.php" class="text-gray-700 hover:text-black mr-4">Mi Panel</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto mt-8">
        <div class="edit-profile-panel">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Editar Perfil</h2>

            

            <form method="post" action="">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="">
                </div>

                <div class="form-group">
                    <label for="password_actual">Contraseña Actual (para cambiar la contraseña):</label>
                    <input type="password" id="password_actual" name="password_actual">
                </div>

                <div class="form-group">
                    <label for="password_nuevo">Nueva Contraseña:</label>
                    <input type="password" id="password_nuevo" name="password_nuevo">
                </div>

                <div class="form-group">
                    <label for="confirmar_password_nuevo">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="confirmar_password_nuevo" name="confirmar_password_nuevo">
                </div>

                <div class="form-actions">
                    <button type="submit">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>