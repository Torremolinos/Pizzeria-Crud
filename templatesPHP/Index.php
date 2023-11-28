<?php
/*github: https://github.com/Torremolinos/Pizzeria-Crud*/
function conectarBD()
{
    //Funcion que nos conecta a la base de datos, tenemos que mandarle la direccion ip del host, el usuario, la clave y el nombre de la BD
    $cadena_conexion = 'mysql:dbname=dwes_t3;host=127.0.0.1';
    $usuario = "root";
    $contrasenia = "";
    try {
        //Se crea el objeto de conexion a la base de datos y se devueve
        $bd = new PDO($cadena_conexion, $usuario, $contrasenia);
        return $bd;
    } catch (PDOException $e) {
        echo "Error conectar BD: " . $e->getMessage();
    }
}

function comprobar_usuario($usuario, $contrasenia)
{
    //Nos conectamos a la BD y lo igualamos a conn que sera donde se guarde la conexion
    $conn = conectarBD();
    //preparar la consulta
    $consulta = $conn->prepare("SELECT usuario, nombre, rol FROM usuarios WHERE usuario =:usuario AND contrasenia =:contrasenia");
    $consulta->bindParam("usuario", $usuario);
    $consulta->bindParam("contrasenia", $contrasenia);
    //lanzar la consulta
    $consulta->execute();

    if ($consulta->rowCount() > 0) {
        $row = $consulta->fetch(PDO::FETCH_ASSOC);
        return array("usuario" => $row['usuario'], "nombre" => $row['nombre'], "rol" => $row['rol']);
    } else
        return FALSE;
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $usu = comprobar_usuario($_POST["usuario"], $_POST["contrasenia"]);
    if ($usu == FALSE) {
        $err = TRUE;
        $usuario = $_POST["usuario"];
    } else {
        session_start();
        $_SESSION['usuario'] = $_POST["usuario"];
        $_SESSION['nombre'] = $usu['nombre'];
        $_SESSION['rol'] = $usu['rol'];
        if ($usu['rol'] == '1') {
            header("Location: zona_admin.php");
        } else if ($usu['rol'] == '2') {
        }
    }
}
$conn = conectarBD();
function listarPizzas($conn)
{
    $consulta = $conn->prepare("SELECT nombre, ingredientes, precio FROM pizzas");
    $consulta->execute();
    echo "<table border='2'>";
    echo "<tr><th>Pizza</th><th>Ingredientes</th><th>Precio</th></tr>";
    echo "<tbody>";
    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "<tr><td>$row[nombre]</td><td>$row[ingredientes]</td><td> $row[precio]€</td></tr>";
    }
    echo "</tbody>";
    echo "</table>";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/index.css">
    <title>Index</title>
</head>

<body>
    <?php if (!isset($_SESSION['usuario'])) : ?>
        <section class="<?php echo !isset($_SESSION['usuario']) ? '' : 'esconde'; ?>">
            <h2>Identificate</h2>
            <div class="formulario">
                <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="POST">
                    <label for="usuario">Usuario</label>
                    <input value="<?php if (isset($usuario)) echo $usuario; ?>" name="usuario">
                    <label for="contrasenia">Contraseña</label>
                    <input type="password" name="contrasenia"> <!-- Este tipo nos permite que salgan puntitos para que no se vea -->
                    <button action="submit">Enviar</button>
                </form>
            </div>
        </section>
    <?php endif; ?>
    <?php if (isset($_SESSION['usuario'])) : ?>
        <section class="<?php echo isset($_SESSION['usuario']) ? '' : 'mostrar'; ?>">
            <!--creo una condicion con un inicio y luego la acabare abajo.-->
            <h1>Hola <?php echo $_SESSION['nombre'] ?></h1>
            <p>Este es nuestro Menú del día si quieres elegir algo dale al enlace "Realizar Pedido".</p>
            <a href='index.php?logout=true'>Cerrar Sesión</a>
        </section>
    <?php endif; ?>
    <br>
    <section>
        <h1>MENÚ</h1>
        <div class="tabla">
            <table border="2">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Ingredientes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $consulta = $conn->prepare("SELECT nombre, ingredientes, precio FROM pizzas");
                    $consulta->execute();
                    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $row) : ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['precio']; ?>€</td>
                            <td><?php echo $row['ingredientes']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php if (isset($_SESSION['usuario'])) : ?>
        <div class=buttones><a class="<?php echo isset($_SESSION['usuario']) ? '' : 'escondido'; ?>" href='pedido.php'>Realizar Pedido</a></div>
    <?php endif; ?>
    <audio src="../assets/audios/musica.mp3" controls></audio>
</body>

</html>