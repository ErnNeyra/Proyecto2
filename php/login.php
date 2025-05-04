<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect | Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos adicionales para este ejemplo (puedes moverlos a style.css) */
        body {
            background-image: linear-gradient(135deg, #fbc02d 0%, #ffeb3b 100%); /* Un fondo llamativo */
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9); /* Fondo blanco semitransparente */
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .login-info {
            background-color: #f9f9f9;
            padding: 4rem;
            border-right: 1px solid #eee;
        }
        .login-info h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .login-form {
            padding: 3rem;
        }
        .login-form h2 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        .form-group input:focus {
            outline: none;
            border-color: #fbc02d;
            box-shadow: 0 0 0 0.2rem rgba(251, 192, 45, 0.25);
        }
        .login-button {
            background-color: #fbc02d;
            color: #333;
            font-weight: bold;
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }
        .login-button:hover {
            background-color: #e0ac28;
        }
        .social-login {
            margin-top: 2rem;
            text-align: center;
        }
        .social-login p {
            color: #777;
            margin-bottom: 1rem;
        }
        .social-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0.6rem 1rem;
            margin: 0 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .social-button:hover {
            background-color: #eee;
            border-color: #ccc;
        }
        .social-button svg {
            margin-right: 0.5rem;
        }
        .register-link {
            margin-top: 1.5rem;
            text-align: center;
            color: #777;
            font-size: 0.9rem;
        }
        .register-link a {
            color: #fbc02d;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .forgot-password a {
            color: #fbc02d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );   
        require('./config.php');
    ?>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $email = $_POST["email"];
            $contrasena = $_POST["contrasena"];

            //si el usuario existe va a devolver una fila con el usuario y contraseña
            $sql = "SELECT * FROM usuario WHERE email = '$email'";

            //
            $resultado = $_conexion -> query($sql);

            //veo lo que muestra el resultado si no se detecta nombre de usuario

            if($resultado -> num_rows == 0){
                echo "<h2>El usuario $email no existe</h2>";
            }else{
                $datosUsuario = $resultado -> fetch_assoc();
                /* Podemos acceder a:
                $datosUsuario["usuario"]
                $datosContrasena["contrasena"]
                */
                //password_verify es la función inversa a el hash
                $accesoConcedido = password_verify($contrasena,$datosUsuario["contrasena"]);
                //compruebo que salga correcto (saldría un booleano TRUE si la contraseña es correcta)
                //var_dump($accesoConcedido);   Si no está la contraseña y el usuario sale FALSE

                if($accesoConcedido){
                    //bien
                    session_start();
                    //La sesión se almacena en el servidor y guarda información del usuario
                    $_SESSION["email"] = $email;
                    //Cookie en el cliente, guarda información del navegador
                    //$_COOKIE["usuario"] = "usuario";

                    header("location: ../index.html");
                    exit;
                }else{
                    echo "<h2>La contraseña ese incorrecta.</h2>";
                }
            }
        }
    ?>
    <div class="login-container w-full max-w-screen-md shadow-lg rounded-lg overflow-hidden md:flex">
        <div class="login-info md:w-1/2 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Bienvenido de Nuevo a We-Connect</h2>
            <p class="mb-4">Conecta con una vibrante comunidad de emprendedores, comparte tus ideas, encuentra colaboradores y haz crecer tu proyecto.</p>
            <p class="mb-4">Accede a tu red de contactos y sigue construyendo relaciones valiosas en el mundo del emprendimiento.</p>
            <p class="text-sm text-gray-600">¿Aún no eres parte de We-Connect? <a href="./registro.php" class="text-yellow-500 font-semibold hover:underline">Regístrate aquí</a>.</p><br><br>
            <button type="button" onclick="window.location.href='../index.html'" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Volver</button>
        </div>

        <div class="login-form md:w-1/2 p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Iniciar Sesión</h2>
            <form class="space-y-4" method="post" action="">
                
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" name="email" id="email" placeholder="Tu dirección de correo electrónico">
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" name="contrasena" id="contrasena" placeholder="Tu contraseña">
                </div>
                <button type="submit" class="login-button">Iniciar Sesión</button>
            </form>

            <div class="forgot-password mt-2">
                <a href="/forgot-password.html">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="social-login mt-6">
                <p>O inicia sesión con:</p>
                <div>
                    <button class="social-button">
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2.04c-5.52 0-10 4.48-10 10 0 4.46 3.26 8.18 7.62 9.62v-6.76h-2.37v-2.86h2.37v-2.06c0-2.34 1.42-3.61 3.46-3.61 1.01.17 1.92.26 2.72.26v3.01h-1.87c-1.14 0-1.67.57-1.67 1.4v1.92h2.84l-.45 2.86h-2.39v6.76c4.36-1.44 7.62-5.16 7.62-9.62 0-5.52-4.48-10-10-10z"/></svg>
                        Facebook
                    </button>
                    <button class="social-button">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path fill="currentColor" d="M21.35 11.1c0-.75-.61-1.36-1.36-1.36h-1.38v-.72c0-.9-.73-1.63-1.63-1.63h-.58c-.9 0-1.63.73-1.63 1.63v.72h-1.38c-.75 0-1.36.61-1.36 1.36v1.38h-.72c-.9 0-1.63.73-1.63 1.63v.58c0 .9.73 1.63 1.63 1.63h.72v1.38c0 .75.61 1.36 1.36 1.36h1.38v.72c.9 0 1.63.73 1.63 1.63h.58c.9 0 1.63-.73 1.63-1.63v-.72h1.38c.75 0 1.36-.61 1.36-1.36v-1.38h.72c.9 0 1.63-.73 1.63-1.63v-.58c0-.9-.73-1.63-1.63-1.63h-.72v-1.38zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3 17.28c-.72 0-1.3-.58-1.3-1.3v-3.48h-2.6v3.48c0 .72-.58 1.3-1.3 1.3h-.58c-.72 0-1.3-.58-1.3-1.3v-3.48h-2.6v3.48c0 .72-.58 1.3-1.3 1.3h-.58c-.72 0-1.3-.58-1.3-1.3v-6.96c0-.72.58-1.3 1.3-1.3h.58c.72 0 1.3.58 1.3 1.3v3.48h2.6v-3.48c0-.72.58-1.3 1.3-1.3h.58c.72 0 1.3.58 1.3 1.3v6.96c0 .72-.58 1.3-1.3 1.3h-.58c-.72 0-1.3-.58-1.3-1.3v-3.48h-2.6v3.48c0 .72-.58 1.3-1.3 1.3h-.58c-.72 0-1.3-.58-1.3-1.3V6.72c0-.72.58-1.3 1.3-1.3h5.2c.72 0 1.3.58 1.3 1.3v10.56z"/></svg>
                        Google
                    </button>
                </div>
            </div>

            <p class="register-link mt-4">¿Aún no tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>
