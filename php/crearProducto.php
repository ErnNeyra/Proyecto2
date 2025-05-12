<?php
    error_reporting(E_ALL);
    ini_set("display_errors",1);
    require ('./util/config.php');
    session_start();

    // Verificar si el usuario ha iniciado sesión
   if (!isset($_SESSION['usuario'])) {
        // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
        header("location: login.php"); // O una página de error de acceso no autorizado
        echo"<h2>Debes iniciar sesión para añadir un producto.</h2>";
        exit();
    }else{
        // Puedes usar $id_usuario para realizar consultas a la base de datos o cualquier otra operación
        echo "<h2>Bienvenid@ ".$_SESSION["usuario"]."</h2>";
        echo "<h2>Tu ID de usuario es: ".$_SESSION["id_usuario"]."</h2>";
    }
    $error = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Validación de campos recibidos
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = (float)$_POST['precio'];
        $usuario = $_SESSION['usuario'];
        $categoria = $_POST['categoria'];

        

        if (strlen($nombre) < 3 || strlen($nombre) > 100) {
            $error = "El nombre debe tener entre 3 y 100 caracteres.";
        } else if (strlen($descripcion) < 10 || strlen($descripcion) > 500) {
            $error = "La descripción debe tener entre 10 y 500 caracteres.";
        } else if ($precio <= 0) {
            $error = "El precio debe ser mayor que 0.";
        } else {
            // Validación de categoría
            $check = $_conexion->prepare("SELECT nombre FROM categoria WHERE nombre = ?");
            $check->bind_param("s", $categoria);
            $check->execute();
            if (!$check->get_result()->num_rows > 0) {
                $error = "Categoría inválida";
            } else {
                // Validación de imagen
                if(isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
                    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
                    $filename = $_FILES["imagen"]["name"];
                    $filetype = $_FILES["imagen"]["type"];
                    $filesize = $_FILES["imagen"]["size"];

                    // Verificar extensión del archivo
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                    if(!array_key_exists($ext, $allowed)) {
                        $error = "Error: Por favor selecciona un formato válido de archivo (JPG o PNG).";
                    }

                    // Verificar tamaño (5MB máximo)
                    $maxsize = 5 * 1024 * 1024;
                    if($filesize > $maxsize) {
                        $error = "Error: El tamaño del archivo supera el límite de 5MB.";
                    }

                    if(empty($error)) {
                        $imagen = $filename;
                        $ubicacionTemporal = $_FILES["imagen"]["tmp_name"];
                        $ubicacionFinal = "../img/$imagen";
                        move_uploaded_file($ubicacionTemporal, $ubicacionFinal);
                    }
                } else {
                    // Si no hay imagen, usar una por defecto
                    $ubicacionFinal = "../img/default-product.png";
                }
            }
        }

        // Si no hay errores, proceder con la inserción
        if(empty($error)) {
            if(isset($nombre) && isset($descripcion) && isset($precio) && isset($usuario) && isset($categoria) && isset($ubicacionFinal)){
                $sql = $_conexion->prepare("INSERT INTO producto
                    (nombre, descripcion, precio, usuario, categoria, imagen)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );
                $sql->bind_param("ssdsss",
                    $nombre, $descripcion, $precio, $usuario, $categoria, $ubicacionFinal
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="listado.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <a class="bg-red-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200" href="logout.php">Cerrar sesión</a>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="panelUsuario.php" class="text-gray-700 hover:text-black mr-4">Mi Panel</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Crear Nuevo Producto o Servicio</h1>
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                    <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG. Tamaño máximo: [establecer límite].</p>
                </div>
                <div class="mb-4">
                    <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                    <select id="categoria" name="categoria" class="form-select form-select-lg">
                    <option value="">---Selecciona una categoría---</option>
                    <?php
                    foreach ($categorias as $categoria): ?>
                        <!-- Ambas formas php las interpreta de la misma manera, está creando un option con el valor $estudio e imprimiendolo en el select -->
                        <option value="<?php echo $categoria;?>"><?= $categoria ?></option>
                    <?php endforeach; ?>
                    </select>
                    <?php if (isset($errorCategoria)) echo "<span class='error'>$errorCategoria</span>"; ?>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Crear Producto</button>
                    <a href="listado.php" class="inline-block align-baseline font-semibold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const precioInput = document.getElementById('precio');
        const nombreInput = document.getElementById('nombre');
        const descripcionInput = document.getElementById('descripcion');

        // Agregar div para mensajes de error antes del formulario
        const errorContainer = document.createElement('div');
        errorContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 hidden';
        errorContainer.setAttribute('role', 'alert');
        form.parentNode.insertBefore(errorContainer, form);

        form.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessages = [];

            // Validación del nombre
            if (nombreInput.value.length < 3 || nombreInput.value.length > 100) {
                errorMessages.push('El nombre debe tener entre 3 y 100 caracteres.');
                isValid = false;
            }

            // Validación de la descripción
            if (descripcionInput.value.length < 10 || descripcionInput.value.length > 500) {
                errorMessages.push('La descripción debe tener entre 10 y 500 caracteres.');
                isValid = false;
            }

            // Validación del precio
            if (precioInput.value <= 0) {
                errorMessages.push('El precio debe ser mayor que 0.');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                errorContainer.innerHTML = errorMessages.map(msg => `<p>${msg}</p>`).join('');
                errorContainer.classList.remove('hidden');
                // Scroll hacia el mensaje de error
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                errorContainer.classList.add('hidden');
            }
        });
    });
</script>

</body>
</html>