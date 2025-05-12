<?php
    session_start();
    require('./util/config.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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

            <?php if (isset($error)): ?>
                <div class="error-message-overall mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="success-message mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data" novalidate>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        value="<?php echo isset($usuario['nombre']) ? htmlspecialchars($usuario['nombre']) : ''; ?>"
                        pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$"
                        required
                        title="El nombre debe tener entre 3 y 50 caracteres y solo puede contener letras y espacios"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo isset($usuario['email']) ? htmlspecialchars($usuario['email']) : ''; ?>"
                        required
                        pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                        title="Por favor, introduce un email válido"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="password_actual">Contraseña Actual (requerida para cambios):</label>
                    <input 
                        type="password" 
                        id="password_actual" 
                        name="password_actual"
                        required
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="password_nuevo">Nueva Contraseña (dejar en blanco si no desea cambiarla):</label>
                    <input 
                        type="password" 
                        id="password_nuevo" 
                        name="password_nuevo"
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                        title="La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="confirmar_password_nuevo">Confirmar Nueva Contraseña:</label>
                    <input 
                        type="password" 
                        id="confirmar_password_nuevo" 
                        name="confirmar_password_nuevo"
                        oninput="this.setCustomValidity(this.value != document.getElementById('password_nuevo').value ? 'Las contraseñas no coinciden' : '')"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción del Negocio:</label>
                    <textarea 
                        id="descripcion" 
                        name="descripcion"
                        maxlength="500"
                        class="w-full p-3 border border-gray-300 rounded-md focus:border-yellow-500 focus:ring-yellow-500"
                        rows="4"><?php echo isset($usuario['descripcion']) ? htmlspecialchars($usuario['descripcion']) : ''; ?></textarea>
                    <button type="button" 
                            id="mejorarDescripcion" 
                            class="mt-2 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                        Mejorar Descripción
                    </button>
                    <div id="sugerenciaDescripcion" class="mt-3 p-3 border rounded-md hidden">
                        <h4 class="font-semibold mb-2">Sugerencia de mejora:</h4>
                        <p id="textoSugerencia" class="text-gray-700"></p>
                        <button type="button" 
                                id="aplicarSugerencia" 
                                class="mt-2 bg-green-500 text-white py-1 px-3 rounded-md hover:bg-green-600 transition duration-200">
                            Aplicar Sugerencia
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ciudad">Ciudad:</label>
                    <input 
                        type="text" 
                        id="ciudad" 
                        name="ciudad"
                        value="<?php echo isset($usuario['ciudad']) ? htmlspecialchars($usuario['ciudad']) : ''; ?>"
                        maxlength="100"
                        class="focus:border-yellow-500 focus:ring-yellow-500">
                </div>

                <div class="form-group">
                    <label for="imagen">Imagen de Perfil:</label>
                    <input 
                        type="file" 
                        id="imagen" 
                        name="imagen"
                        accept="image/jpeg,image/png"
                        class="w-full p-3 border border-gray-300 rounded-md focus:border-yellow-500 focus:ring-yellow-500">
                    <p class="text-sm text-gray-500 mt-1">Formatos permitidos: JPG, PNG. Tamaño máximo: 5MB</p>
                </div>

                <div class="form-actions">
                    <button type="submit" class="hover:bg-yellow-600 transition-colors duration-300">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script>
        document.getElementById('mejorarDescripcion').addEventListener('click', async function() {
            const descripcion = document.getElementById('descripcion').value;
            const sugerenciaDiv = document.getElementById('sugerenciaDescripcion');
            const textoSugerencia = document.getElementById('textoSugerencia');
            
            if (!descripcion.trim()) {
                alert('Por favor, ingrese una descripción primero.');
                return;
            }

            try {
                this.disabled = true;
                this.textContent = 'Procesando...';

                const response = await fetch('util/mejorar_descripcion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ descripcion: descripcion })
                });

                const data = await response.json();

                if (data.success) {
                    textoSugerencia.textContent = data.success;
                    sugerenciaDiv.classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.error || 'No se pudo procesar la solicitud'));
                }
            } catch (error) {
                alert('Error al procesar la solicitud');
            } finally {
                this.disabled = false;
                this.textContent = 'Mejorar Descripción';
            }
        });

        document.getElementById('aplicarSugerencia').addEventListener('click', function() {
            const descripcion = document.getElementById('descripcion');
            const sugerencia = document.getElementById('textoSugerencia').textContent;
            descripcion.value = sugerencia;
            document.getElementById('sugerenciaDescripcion').classList.add('hidden');
        });
    </script>
</body>
</html>