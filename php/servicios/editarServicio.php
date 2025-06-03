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

    // Obtener datos actuales del servicio
    $idServicio = $_GET["id_servicio"] ?? null;
    if (!$idServicio) {
        header("Location: servicio.php");
        exit;
    }

    $sql = "SELECT * FROM servicio WHERE id_servicio = ?";
    $stmt = $_conexion->prepare($sql);
    $stmt->bind_param("i", $idServicio);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $servicio = $resultado->fetch_assoc();

    if (!$servicio) {
        echo "<p class='text-red-500'>Servicio no encontrado.</p>";
        exit;
    }

    if ($_SESSION["usuario"]["usuario"] !== $servicio["usuario"]) {
        // Redirigir o mostrar error si no es el propietario
        header("Location: producto.php");
        // También puedes mostrar un mensaje de error:
        echo "<p class='text-red-500'>No tienes permiso para editar este servicio.</p>";
        exit;
    }

    // Variables para mostrar en el formulario
    $nombre = $servicio["nombre"];
    $descripcion = $servicio["descripcion"];
    $precio = $servicio["precio"];
    $imagenActual = $servicio["imagen"];
    $categoria = $servicio["categoria"];

    $errorNombre = $errorDescripcion = $errorPrecio = $errorCategoria = $errorImagen = $success = "";//si no hay errores 

    // Procesar el formulario si se envía
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validación de nombre
        $tmpNombre = depurar(ucwords(strtolower($_POST['nombre'])));
        if ($tmpNombre == "") {
            $errorNombre = "El nombre es obligatorio";
        } elseif (strlen($tmpNombre) < 3 || strlen($tmpNombre) > 100) {
            $errorNombre = "El nombre debe tener entre 3 y 100 caracteres";
        } else {
            $nombre = $tmpNombre;
        }

        // Validación de descripción
        $tmpDescripcion = depurar(ucwords(strtolower($_POST['descripcion'])));
        if ($tmpDescripcion == "") {
            $errorDescripcion = "La descripción es obligatoria";
        } elseif (strlen($tmpDescripcion) < 10 || strlen($tmpDescripcion) > 500) {
            $errorDescripcion = "La descripción debe tener entre 10 y 500 caracteres";
        } else {
            $descripcion = $tmpDescripcion;
        }

        // Validación de precio
        $tmpPrecio = depurar($_POST['precio']);
        if ($tmpPrecio == "") {
            $errorPrecio = "El precio es obligatorio";
        } elseif (filter_var($tmpPrecio, FILTER_VALIDATE_FLOAT) === FALSE) {
            $errorPrecio = "El precio debe ser un número válido";
        } elseif ($tmpPrecio <= 0) {
            $errorPrecio = "El precio debe ser mayor que 0";
        } else {
            $precio = $tmpPrecio;
        }

        // Validación de categoría
        $categoria = $_POST['categoria'];
        $checkCategoria = $_conexion->prepare("SELECT nombre FROM categoria WHERE nombre = ?");
        $checkCategoria->bind_param("s", $categoria);
        $checkCategoria->execute();
        if (!$checkCategoria->get_result()->num_rows > 0) {
            $errorCategoria = "Categoría inválida";
        }

        // Validación y procesamiento de imagen
        $imagenFinal = $imagenActual;
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {
            $permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $imagenTipo = $_FILES["imagen"]["type"];
            if (!in_array($imagenTipo, $permitidos)) {
                $errorImagen = "Solo se permiten imágenes JPG, PNG, GIF o WebP.";
            } else {
                $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
                $nombreImagen = uniqid('img_', true) . '.' . $extension;
                $ubicacionFinal = "../util/img/$nombreImagen";
                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ubicacionFinal)) {
                    // Eliminar imagen anterior si no es la de por defecto
                    if (!empty($imagenActual) && file_exists($imagenActual) && $imagenActual != '../util/img/usuario.png') {
                        unlink($imagenActual);
                    }
                    $imagenFinal = $ubicacionFinal;
                } else {
                    $errorImagen = "Error al subir la imagen.";
                }
            }
        }

        // Si no hay errores, actualizar servicio
        if (empty($errorNombre) && empty($errorDescripcion) && empty($errorPrecio) && empty($errorCategoria) && empty($errorImagen)) {
            $sql = "UPDATE servicio SET nombre=?, descripcion=?, precio=?, categoria=?, imagen=? WHERE id_servicio=?";
            $stmt = $_conexion->prepare($sql);
            $stmt->bind_param("ssdssi", $nombre, $descripcion, $precio, $categoria, $imagenFinal, $idServicio);
            if ($stmt->execute()) {
                $success = "Servicio actualizado con éxito.";
                // Recargar datos actualizados
                $servicio["nombre"] = $nombre;
                $servicio["descripcion"] = $descripcion;
                $servicio["precio"] = $precio;
                $servicio["categoria"] = $categoria;
                $servicio["imagen"] = $imagenFinal;
            } else {
                $errorImagen = "Error al actualizar el servicio: " . $_conexion->error;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/nuevo.css">
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
    <!-- favicon -->
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
<header class="bg-white shadow-md">
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
</header>

<main class="container mx-auto py-12 px-6 flex-grow">
    <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Editar Servicio</h1>
        <?php if ($success): ?>
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4"><?= $success ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($servicio["nombre"]) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <?php if ($errorNombre) echo "<span class='text-red-500 text-xs italic'>$errorNombre</span>"; ?>
            </div>
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?= htmlspecialchars($servicio['descripcion'] ?? '') ?></textarea>
                <?php if ($errorDescripcion) echo "<span class='text-red-500 text-xs italic'>$errorDescripcion</span>"; ?>
            </div>
            <div class="mb-4">
                <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" value="<?= htmlspecialchars($servicio["precio"]) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <?php if ($errorPrecio) echo "<span class='text-red-500 text-xs italic'>$errorPrecio</span>"; ?>
            </div>
            <div class="mb-4">
                <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG, GIF, WebP. Tamaño máximo: 2MB.</p>
                <?php if (!empty($servicio['imagen'])): ?>
                    <p class="text-sm mt-2">Imagen actual: <a href="<?= htmlspecialchars($servicio["imagen"]) ?>" target="_blank" class="text-blue-500 underline">Ver imagen</a></p>
                <?php endif; ?>
                <?php if ($errorImagen) echo "<span class='text-red-500 text-xs italic'>$errorImagen</span>"; ?>
            </div>
            <?php
                $sql = "SELECT nombre FROM categoria";
                $resultado = $_conexion -> query($sql);
                $categorias = [];
                while($fila = $resultado -> fetch_assoc()){
                    array_push($categorias, $fila["nombre"]);
                }
            ?>
            <div class="mb-4">
                <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                <select required id="categoria" name="categoria" class="form-select form-select-lg">
                    <option value="">---Selecciona una categoría---</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $servicio["categoria"] == $cat ? "selected" : "" ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($errorCategoria) echo "<span class='text-red-500 text-xs italic'>$errorCategoria</span>"; ?>
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
</body>
</html>
