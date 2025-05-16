<?php
    error_reporting(E_ALL);
    ini_set("display_errors",1);
    require ('../util/config.php');
    require ('../util/depurar.php');
    session_start();

    // Verificar si el usuario ha iniciado sesión
   if (!isset($_SESSION['usuario'])) {
        // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
        header("location: ../usuarios/login.php"); // O una página de error de acceso no autorizado
        echo"<h2>Debes iniciar sesión para añadir un producto.</h2>";
        exit();
    }else{
        // Puedes usar $id_usuario para realizar consultas a la base de datos o cualquier otra operación
        echo "<h2>Bienvenid@ ".$_SESSION["usuario"]["usuario"]."</h2>";
        echo "<h2>Tu ID de usuario es: ".$_SESSION["usuario"]["id_usuario"]."</h2>";
    }
    $error = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Recibo el usuario con la sesión
        $usuarioSesion = $_SESSION['usuario'];

        // Validación de campos
        //Valido el nombre del producto
        $tmpNombre = depurar(ucwords(strtolower($_POST['nombre'])));
        if($tmpNombre == "") {
            $errorNombre = "El nombre es obligatorio";
        } else {
            if(strlen($tmpNombre) < 3 || strlen($tmpNombre) > 100) {
                $errorNombre = "El nombre debe tener entre 3 y 100 caracteres";
            } else {
                $nombre = $tmpNombre;
            }
        }
        //Valido la descripción del producto
        $tmpDescripcion = depurar(ucwords(strtolower($_POST['descripcion'])));
        if($tmpDescripcion == "") {
            $errorDescripcion = "La descripción es obligatoria";
        } else {
            if(strlen($tmpDescripcion) < 10 || strlen($tmpDescripcion) > 500) {
                $errorDescripcion = "La descripción debe tener entre 10 y 500 caracteres";
            } else {
                $descripcion = $tmpDescripcion;
            }
        }
        //Valido el precio del producto
        $tmpPrecio = depurar($_POST['precio']);
        if($tmpPrecio == "") {
            $errorPrecio = "El precio es obligatorio";
        } else {
            if(filter_var($tmpPrecio, FILTER_VALIDATE_FLOAT) === FALSE) {
                $errorPrecio = "El precio debe ser un número válido";
            } else {
                if($tmpPrecio <= 0) {
                    $errorPrecio = "El precio debe ser mayor que 0";
                } else {
                    $precio = $tmpPrecio;
                }
            }
        }


        //Valido la categoria del producto
        $categoria = $_POST['categoria'];

        // Validación de categoría
        $checkCategoria = $_conexion->prepare("SELECT nombre FROM categoria WHERE nombre = ?");
        $checkCategoria->bind_param("s", $categoria);
        $checkCategoria->execute();
        if (!$checkCategoria->get_result()->num_rows > 0) {
            $errorCategoria = "Categoría inválida";
        }


        //Validación de imagen
        //$_FILES es un array BIDIMENSIONAL, mientras que $_POST es un array UNIDIMENSIONAL
        
        $imagen = depurar($_FILES["imagen"]["name"]);
        $ubicacionTemporal= depurar($_FILES["imagen"]["tmp_name"]);
        $imagenTipo = $_FILES["imagen"]["type"];
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
        // Procesar imagen
        } else {
            $errorImagen = "Debe seleccionar una imagen válida.";
        }
        $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($imagenTipo, $permitidos)) {
            $errorImagen = "Solo se permiten imágenes JPG, PNG, GIF o WebP.";
        }
        $extension = pathinfo($imagen, PATHINFO_EXTENSION);
        $nombreImagen = uniqid('img_', true) . '.' . $extension;
        $ubicacionFinal = "../util/img/$nombreImagen";


        //mueve el archivo que se ha cargado de una ubicación a otra
        move_uploaded_file($ubicacionTemporal, $ubicacionFinal);

        // Si no hay errores, proceder con la inserción
        if(empty($error)) {
            if(isset($nombre) && isset($descripcion) && isset($precio) && isset($usuarioSesion["usuario"]) && isset($categoria) && isset($ubicacionFinal)){
                $sql = $_conexion->prepare("INSERT INTO producto
                    (nombre, descripcion, precio, usuario, categoria, imagen)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );
                $sql->bind_param("ssdsss",
                    $nombre, $descripcion, $precio, $usuarioSesion["usuario"], $categoria, $ubicacionFinal
                );
                if ($sql->execute()) {
                    $success = "Producto creado con éxito.";
                } else {
                    $error = "Error al crear el producto: " . $_conexion->error;
                }
            }
        }

    }

    $sql = "SELECT nombre FROM categoria";
    $resultado = $_conexion -> query($sql);
    $categorias = [];
    /* fetch_assoc() devuelve una fila de resultados como un array asociativo. Esto significa que podrás acceder
    a cada columna de la fila por su nombre */
    while($fila = $resultado -> fetch_assoc()){
        array_push($categorias, $fila["nombre"]);
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Producto | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <?php
                    if(isset($_SESSION["usuario"])){
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
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Crear Nuevo Producto o Servicio</h1>
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
                    <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <?php if (isset($errorNombre)) echo "<span class='text-red-500 text-xs italic'>$errorNombre</span>"; ?>
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                    <?php if (isset($errorDescripcion)) echo "<span class='text-red-500 text-xs italic'>$errorDescripcion</span>"; ?>
                    
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
                    <input type="number" id="precio" name="precio" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <?php if (isset($errorPrecio)) echo "<span class='text-red-500 text-xs italic'>$errorPrecio</span>"; ?>
                </div>
                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                    <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG. Tamaño máximo: [establecer límite].</p>
                </div>
                <div class="mb-4">
                    <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                    <select required id="categoria" name="categoria" class="form-select form-select-lg">
                    <option value="">---Selecciona una categoría---</option>
                    <?php
                    foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria;?>"><?= $categoria ?></option>
                    <?php endforeach; ?>
                    </select>
                    <?php if (isset($errorCategoria)) echo "<span class='text-red-500 text-xs italic'>$errorCategoria</span>"; ?>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Crear Producto</button>
                    <a href="producto.php" class="inline-block align-baseline font-semibold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        &copy; 2025 We-Connect. Todos los derechos reservados.
    </footer>
</body>
</html>
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
    <script src="../../js/script2.js"></script>
</body>
</html>