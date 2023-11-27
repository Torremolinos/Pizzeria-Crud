<!DOCTYPE html>
<html>

<head>
    <title>Añadir Pizza</title>
</head>

<body>
    <h1>Añadir Nueva Pizza</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        Nombre: <input type="text" name="nombre"><br>
        Coste: <input type="number" step="0.01" name="coste"><br>
        Precio: <input type="number" step="0.01" name="precio"><br>
        Ingredientes: <textarea name="ingredientes"></textarea><br>
        <input type="submit" name="submit" value="Añadir Pizza">
    </form>
</body>

</html>
<?php

// Función para conectar a la BD y otras funciones
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
    // Preparar consulta
    $consulta = $conn->prepare("SELECT nombre, precio FROM pizzas");
    // Ejecutar consulta
    $consulta->execute();

    // Listar las pizzas
    foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $row) {
        echo $row["nombre"] . ". " . $row["precio"] . "€.<br>";
    }
}

// Listar las pizzas
echo "<h1>Nuestras Pizzas</h1>";
listarPizzas($conn);
?>