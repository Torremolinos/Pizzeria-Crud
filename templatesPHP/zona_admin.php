<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php?redirigido=true");
}
function conectarBD()
{
    $cadena_conexion = 'mysql:dbname=dwes_t3;host=127.0.0.1';
    $usuario = "root";
    $contrasenia = "";

    try {
        $bd = new PDO($cadena_conexion, $usuario, $contrasenia);
        return $bd;
    } catch (PDOException $e) {
        echo "Error conectando a la bd: " . $e->getMessage();
    }
}

// Función para conectar a la BD y otras funciones


// Conectar a la base de datos
$conn = conectarBD();

// Revisa si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombrePizza = $_POST['nombre'];
    $costePizza = $_POST['coste'];
    $precioPizza = $_POST['precio'];
    $ingredientesPizza = $_POST['ingredientes'];

    // Preparar la consulta SQL
    $insertar = $conn->prepare("INSERT INTO pizzas (nombre, coste, precio, ingredientes) VALUES (:nombre, :coste, :precio, :ingredientes)");

    // Vincular los parámetros
    $insertar->bindParam(':nombre', $nombrePizza);
    $insertar->bindParam(':coste', $costePizza);
    $insertar->bindParam(':precio', $precioPizza);
    $insertar->bindParam(':ingredientes', $ingredientesPizza);

    // Ejecutar la consulta
    $insertar->execute();
}

// Función para listar pizzas
function listarPizzas($conn)
{
    $consulta = $conn->prepare("SELECT nombre, ingredientes, precio FROM pizzas");
    $consulta->execute();
    echo "<table border='2'>";
    echo "<tr><th>Pizza</th><th>Ingredientes</th><th>Precio</th><th>Acciones Admin</th></tr>";
    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo "<tr>";
        echo "<td>$row[nombre]</td><td>$row[ingredientes]</td><td> $row[precio]€</td>";
        echo "<td><button class='edit-btn'>Editar</button>";
        echo "<button class='delete-btn'>Borrar</button></td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona Admin</title>
    <style>
        * {
            margin: 0%;
            padding: 0%;
            box-sizing: border-box;
        }

        body {
            color: wheat;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
            background: url('../assets/img/menupiz.jpg');
            /* Reemplaza 'ruta/a/tu/imagen.jpg' con la ruta correcta de tu imagen */
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-size: cover;

        }

        .formulario {
            display: flex;
            text-align: center;
            padding: 4px;
            margin: 4px;
            display: flex;
            align-items: center;
            justify-content: center;

        }

        .tabla {
            display: flex;
            justify-content: center;
        }

        h1 {
            margin-top: 20px;
            text-align: center;
        }

        h2 {
            padding: 4px;
            margin: 4px;
        }

        table {
            border-collapse: collapse;
            text-align: center;
            background: burlywood;
            border: none;
            margin: 4px;
        }

        th,
        td {
            color: black;
            padding: 4px;
        }

        td:hover {
            transform: scale(1.5);
            transition: transform 0.3s ease;
            background-color: whitesmoke;
        }

        th:hover {
            transform: scale(1.5);
            transition: transform 0.3s ease;
            background-color: whitesmoke;
        }
        a {
            display: flex;
            align-content: center;
            justify-content: center;
            padding: 10px;
            color:beige;
        }
</style>
</head>

<body>
    <h1>Bienvenido, <?php echo $_SESSION['nombre'] ?></h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        Nombre: <input type="text" name="nombre"><br>
        Coste: <input type="number" step="0.01" name="coste"><br>
        Precio: <input type="number" step="0.01" name="precio"><br>
        Ingredientes: <textarea name="ingredientes"></textarea><br>
        <input type="submit" name="submit" value="Añadir Pizza">
    </form>
</body>

</html>