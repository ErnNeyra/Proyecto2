<?php
    error_reporting(E_ALL);
    ini_set("display_errors",1);
    require ('../util/config.php');
    require ('../util/depurar.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["usuario"]["usuario"])) {
        //CUIDADO AMIGO esta función es peligrosa, tiene que ejecutarse antes de que
        //se ejecute el código body
        header("location: ../usuarios/login.php");
        exit;
    }

    $errorNombre = $errorDescripcion = $errorPrecio = $errorCategoria = $errorImagen = $error = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuarioSesion = $_SESSION['usuario'];

        // Validación del nombre
        $tmpNombre = depurar(ucwords(strtolower($_POST['nombre'] ?? "")));
        if($tmpNombre == "") {
            $errorNombre = "El nombre es obligatorio";
        } elseif(strlen($tmpNombre) < 3 || strlen($tmpNombre) > 100) {
            $errorNombre = "El nombre debe tener entre 3 y 100 caracteres";
        } else {
            $nombre = $tmpNombre;
        }

        // Validación de la descripción
        $tmpDescripcion = depurar(ucwords(strtolower($_POST['descripcion'] ?? "")));
        if($tmpDescripcion == "") {
            $errorDescripcion = "La descripción es obligatoria";
        } elseif(strlen($tmpDescripcion) < 10 || strlen($tmpDescripcion) > 500) {
            $errorDescripcion = "La descripción debe tener entre 10 y 500 caracteres";
        } else {
            $descripcion = $tmpDescripcion;
        }

        // Validación del precio con límite razonable
        $tmpPrecio = depurar($_POST['precio'] ?? "");
        if($tmpPrecio == "") {
            $errorPrecio = "El precio es obligatorio";
        } elseif(filter_var($tmpPrecio, FILTER_VALIDATE_FLOAT) === FALSE) {
            $errorPrecio = "El precio debe ser un número válido";
        } elseif($tmpPrecio < 1) {
            $errorPrecio = "El precio debe ser al menos 1 €";
        } elseif($tmpPrecio > 1000000) {
            $errorPrecio = "El precio no puede superar los 1.000.000 €";
        } else {
            $precio = $tmpPrecio;
        }

        // Validación de la categoría
        $categoria = $_POST['categoria'] ?? "";
        $checkCategoria = $_conexion->prepare("SELECT nombre FROM categoria WHERE nombre = ?");
        $checkCategoria->bind_param("s", $categoria);
        $checkCategoria->execute();
        if (!$checkCategoria->get_result()->num_rows > 0) {
            $errorCategoria = "Categoría inválida";
        }

        // Validación de imagen
        $errorImagen = "";
        $ubicacionFinal = ""; // Variable para guardar la ruta de la imagen

        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $imagenTipo = $_FILES["imagen"]["type"];
            $imagenTamano = $_FILES["imagen"]["size"];
            $maxTamano = 2 * 1024 * 1024; // 2MB

            // Validar tipo de archivo
            if (!in_array($imagenTipo, $permitidos)) {
                $errorImagen = "Solo se permiten imágenes JPG, PNG, GIF o WebP.";
            }
            // Validar tamaño
            elseif ($imagenTamano > $maxTamano) {
                $errorImagen = "La imagen no puede superar los 2MB.";
            }
            else {
                // Generar nombre único
                $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
                $nombreImagen = uniqid('img_', true) . '.' . $extension;
                $directorioDestino = "../util/img/";
                $ubicacionFinal = $directorioDestino . $nombreImagen;
                // Mover archivo
                if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $ubicacionFinal)) {
                    $errorImagen = "Error al subir la imagen.";
                }
            }
        } else {
            $errorImagen = "Debes seleccionar una imagen válida.";
        }

        // Si no hay errores, proceder con la inserción
        if(
            empty($errorNombre) &&
            empty($errorDescripcion) &&
            empty($errorPrecio) &&
            empty($errorCategoria) &&
            empty($errorImagen)
        ) {
            $sql = $_conexion->prepare("INSERT INTO servicio (nombre, descripcion, precio, usuario, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?)");
            $sql->bind_param("ssdsss", $nombre, $descripcion, $precio, $usuarioSesion["usuario"], $categoria, $ubicacionFinal);
            if ($sql->execute()) {
                $success = "Servicio creado con éxito.";
            } else {
                $error = "Error al crear el servicio: " . $_conexion->error;
            }
        }
    }

    // Cargar categorías para el formulario
    $sql = "SELECT nombre FROM categoria";
    $resultado = $_conexion -> query($sql);
    $categorias = [];
    while($fila = $resultado -> fetch_assoc()){
        array_push($categorias, $fila["nombre"]);
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/nuevo.css">
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
    <!-- favicon -->
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto py-4 px-6 flex items-center justify-between">
        <a href="../../index.php" class="logo inline-block">
            <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
        </a>
        <nav class="flex items-center space-x-4">
             <a href="../recursos/recursos.php" class="text-gray-700 hover:text-black mr-4 font-semibold">Recursos</a>
                <a href="../productos/producto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Productos</a>
                <a href="../servicios/servicio.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Servicios</a>
                <a href="../contacto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Contacto</a>
            <?php
                if (isset($_SESSION["usuario"])) {
                    $imagenPerfil = isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])
                        ? htmlspecialchars($_SESSION['usuario']['foto_perfil'])
                        : '../util/img/usuario.png';

                    echo '<div class="relative">';
                    echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                    echo '        <img class="h-8 w-8 rounded-full object-cover" src="' . $imagenPerfil . '" alt="Imagen de Perfil">';
                    echo '        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                    echo '            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />';
                    echo '        </svg>';
                    echo '    </button>';

                    echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                    echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Panel</a>';
                    echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                    echo '        <hr class="border-gray-200">';
                    echo '        <a href="../comunidad/tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Tablón Comunidad</a>';
                    echo '        <a href="../categoria/index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categoría</a>';
                    echo '        <hr class="border-red-200">';
                    echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                    echo '    </div>';
                    echo '</div>';
                } else {
                    echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                }
            ?>
        </nav>
    </div>
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        require('../util/config.php');
    ?>
</header>


    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Crear Nuevo Servicio</h1>
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Éxito:</strong>
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <?php if (!empty($errorNombre)) echo "<span class='text-red-500 text-xs italic'>$errorNombre</span>"; ?>
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                    <?php if (!empty($errorDescripcion)) echo "<span class='text-red-500 text-xs italic'>$errorDescripcion</span>"; ?>
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
                    <input type="number" id="precio" name="precio" step="0.01" value="<?php echo htmlspecialchars($_POST['precio'] ?? ''); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <?php if (!empty($errorPrecio)) echo "<span class='text-red-500 text-xs italic'>$errorPrecio</span>"; ?>
                </div>
                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (obligatoria):</label>
                    <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 2MB.</p>
                    <?php if (!empty($errorImagen)) echo "<span class='text-red-500 text-xs italic'>$errorImagen</span>"; ?>
                </div>
                <div class="mb-4">
                    <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                    <select required id="categoria" name="categoria" class="form-select form-select-lg">
                        <option value="">---Selecciona una categoría---</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php if(($_POST['categoria'] ?? '') == $cat) echo "selected"; ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errorCategoria)) echo "<span class='text-red-500 text-xs italic'>$errorCategoria</span>"; ?>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Crear Servicio</button>
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
        // Funcionalidad para mejorar descripción
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

                const response = await fetch('../util/mejorar_descripcion.php', {
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
