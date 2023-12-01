<?php
function conectarBD()
{
    //Funcion que nos conecta a la base de datos, tenemos que mandarle la direccion ip del host, el usuario, la clave y el nombre de la BD
    $cadena_conexion = 'mysql:dbname=dwes_t3;host=127.0.0.1';
    $usuario = "root";
    $contrasenia = "";
    try {
        //Se crea el objeto de conexion a la base de datos y se devuelve
        $bd = new PDO($cadena_conexion, $usuario, $contrasenia);
        return $bd;
    } catch (PDOException $e) {
        echo "Error conectar BD: " . $e->getMessage();
    }
}

function usuarioExiste($usuario)
{
    $conexion = conectarBD();
    $consulta = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario");
    $consulta->bindParam(':usuario', $usuario);
    $consulta->execute();
    $resultado = $consulta->fetchColumn();
    return $resultado > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasenia = $_POST['contrasenia'];
    $rol = $_POST['rol'];
    $usuario = $_POST['usuario'];

    // Verificar que los datos no estén vacíos antes de insertar
    if (!empty($nombre) && !empty($correo) && !empty($contrasenia) && !empty($rol) && !empty($usuario)) {
        // Verificar si el usuario ya existe
        if (usuarioExiste($usuario)) {
            echo "El nombre de usuario ya existe. Por favor, elige otro.";
        } else {
            // Preparar la consulta SQL
            $conexion = conectarBD(); // Usamos la función para conectar a la base de datos
            $insertar = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasenia, rol, usuario) VALUES (:nombre, :correo, :contrasenia, :rol, :usuario)");

            // Vincular los parámetros
            $insertar->bindParam(':nombre', $nombre);
            $insertar->bindParam(':correo', $correo);
            $insertar->bindParam(':contrasenia', $contrasenia);
            $insertar->bindParam(':rol', $rol);
            $insertar->bindParam(':usuario', $usuario);

            $insertar->execute();
            echo "Usuario Registrado Correctamente.";
        }
    } else {
        echo "Por favor, completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../styles/index.css">
    <style>
        button {
            padding: 10px 20px;
            background-color: burlywood;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: bisque;
        }
    </style>
</head>

<body>
    <h1>PIZZERIA TRATORIA NAPOLÉS</h1>
    <h1>REGISTRO</h1>
    <h2>Hola, completa todos los campos para poder registrarte</h2>
    <div class="formulario">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="usuario">Usuario</label>
            <input value="<?php if (isset($usuario)) echo $usuario; ?>" name="usuario">
            <br />
            <label for="contrasenia">Contraseña</label>
            <input type="password" name="contrasenia">
            <br />
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre">
            <br />
            <label for="rol">Rol</label>
            <input type="text" name="rol">
            <br />
            <label for="correo">Correo</label>
            <input type="email" name="correo">
            <br />
            <button type="submit">Enviar</button>
        </form>
    </div>
    <a href="Index.php">Volver a la página principal</a>
</body>

</html>